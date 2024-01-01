@extends('layouts.chips.main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Dropdown to Select Analysis -->
            <label for="analysis_id">Select Analysis:</label>
            <select id="analysis_id" class="form-select mb-3">
                <option value="">Select an Analysis</option>
                @foreach($analyses as $analysis)
                    @if(!empty($analysis->name))
                        <option value="{{ $analysis->id }}">{{ $analysis->name }}</option>
                    @endif
                @endforeach
            </select>
            <div class="col-md-12">
                <div class="card mb-5">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#basicInfo">
                                Static Analysis Information
                            </button>
                        </h2>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <!-- Card fields go here, dynamically populated by JavaScript -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap and jQuery scripts for the collapse functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <script>
        const analysisSelect = document.getElementById('analysis_id');

        // An array to store Analysis IDs and their corresponding data
        const analysisData = [
                @foreach($analyses as $analysis)
            {
                id: '{{ $analysis->id }}',
                analysis_id: @if(isset($analysis->parsed_data['analysis_id']) && is_string($analysis->parsed_data['analysis_id'])) '{{ htmlspecialchars($analysis->parsed_data['analysis_id']) }}' @else '' @endif,
                score: @if(isset($analysis->parsed_data['score']) && is_string($analysis->parsed_data['score'])) '{{ htmlspecialchars($analysis->parsed_data['score']) }}' @else '' @endif,
                category: @if(isset($analysis->parsed_data['category']) && is_string($analysis->parsed_data['category'])) '{{ htmlspecialchars($analysis->parsed_data['category']) }}' @else '' @endif,
                target: {
                    filename: @if(isset($analysis->parsed_data['target']['filename']) && is_string($analysis->parsed_data['target']['filename'])) '{{ htmlspecialchars($analysis->parsed_data['target']['filename']) }}' @else '' @endif,
                    orig_filename: @if(isset($analysis->parsed_data['target']['orig_filename']) && is_string($analysis->parsed_data['target']['orig_filename'])) '{{ htmlspecialchars($analysis->parsed_data['target']['orig_filename']) }}' @else '' @endif,
                    platforms: [
                            @if(isset($analysis->parsed_data['target']['platforms'][0]))
                        {
                            platform: @if(is_string($analysis->parsed_data['target']['platforms'][0]['platform'])) '{{ htmlspecialchars($analysis->parsed_data['target']['platforms'][0]['platform']) }}' @else '' @endif,
                            os_version: @if(is_string($analysis->parsed_data['target']['platforms'][0]['os_version'])) '{{ htmlspecialchars($analysis->parsed_data['target']['platforms'][0]['os_version']) }}' @else '' @endif,
                        }
                        @endif
                    ],
                    size: @if(isset($analysis->parsed_data['target']['size']) && is_string($analysis->parsed_data['target']['size'])) '{{ htmlspecialchars($analysis->parsed_data['target']['size']) }}' @else '' @endif,
                    filetype: @if(isset($analysis->parsed_data['target']['filetype']) && is_string($analysis->parsed_data['target']['filetype'])) '{{ htmlspecialchars($analysis->parsed_data['target']['filetype']) }}' @else '' @endif,
                    media_type: @if(isset($analysis->parsed_data['target']['media_type']) && is_string($analysis->parsed_data['target']['media_type'])) '{{ htmlspecialchars($analysis->parsed_data['target']['media_type']) }}' @else '' @endif,
                    sha256: @if(isset($analysis->parsed_data['target']['sha256']) && is_string($analysis->parsed_data['target']['sha256'])) '{{ htmlspecialchars($analysis->parsed_data['target']['sha256']) }}' @else '' @endif,
                    sha1: @if(isset($analysis->parsed_data['target']['sha1']) && is_string($analysis->parsed_data['target']['sha1'])) '{{ htmlspecialchars($analysis->parsed_data['target']['sha1']) }}' @else '' @endif,
                    md5: @if(isset($analysis->parsed_data['target']['md5']) && is_string($analysis->parsed_data['target']['md5'])) '{{ htmlspecialchars($analysis->parsed_data['target']['md5']) }}' @else '' @endif,
                },
                static: {
                    pe: {
                        peid_signatures: @if(isset($analysis->parsed_data['static']['pe']['peid_signatures']) && is_string($analysis->parsed_data['static']['pe']['peid_signatures'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['peid_signatures']) }}' @else '' @endif,
                        pe_imports: @if(isset($analysis->parsed_data['static']['pe']['pe_imports']) && is_string($analysis->parsed_data['static']['pe']['pe_imports'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['pe_imports']) }}' @else '' @endif,
                        pe_exports: @if(isset($analysis->parsed_data['static']['pe']['pe_exports']) && is_string($analysis->parsed_data['static']['pe']['pe_exports'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['pe_exports']) }}' @else '' @endif,
                        pe_sections: @if(isset($analysis->parsed_data['static']['pe']['pe_sections']) && is_string($analysis->parsed_data['static']['pe']['pe_sections'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['pe_sections']) }}' @else '' @endif,
                        pe_resources: @if(isset($analysis->parsed_data['static']['pe']['pe_resources']) && is_string($analysis->parsed_data['static']['pe']['pe_resources'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['pe_resources']) }}' @else '' @endif,
                        pe_versioninfo: @if(isset($analysis->parsed_data['static']['pe']['pe_versioninfo']) && is_string($analysis->parsed_data['static']['pe']['pe_versioninfo'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['pe_versioninfo']) }}' @else '' @endif,
                        pe_imphash: @if(isset($analysis->parsed_data['static']['pe']['pe_imphash']) && is_string($analysis->parsed_data['static']['pe']['pe_imphash'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['pe_imphash']) }}' @else '' @endif,
                        pe_timestamp: @if(isset($analysis->parsed_data['static']['pe']['pe_timestamp']) && is_string($analysis->parsed_data['static']['pe']['pe_timestamp'])) '{{ htmlspecialchars($analysis->parsed_data['static']['pe']['pe_timestamp']) }}' @else '' @endif,
                    },
                },
            },
            @endforeach
        ];

        // Helper function to create a list item with a label and value
        function createListItem(label, value) {
            const li = document.createElement("li");
            li.innerHTML = `<strong>${label}:</strong> ${value}`;
            return li;
        }

        // Helper function to populate a section with a list of items
        function populateSection(sectionId, data, createItemCallback) {
            const sectionList = document.getElementById(sectionId);
            sectionList.innerHTML = "";

            data.forEach(item => {
                const sectionItem = createItemCallback(item);
                sectionList.appendChild(sectionItem);
            });
        }

        // Function to populate card fields with data
        function populateCardFields(data) {
            // Basic Information
            document.getElementById("analysis_id_value").textContent = data.analysis_id;
            document.getElementById("score_value").textContent = data.score;
            document.getElementById("category_value").textContent = data.category;

            // Target Information
            document.getElementById("filename_value").textContent = data.target.filename;
            document.getElementById("orig_filename_value").textContent = data.target.orig_filename;

            const platformsList = document.getElementById("platforms_value");
            platformsList.innerHTML = "";
            data.target.platforms.forEach(platform => {
                const li = createListItem("Platform", platform.platform);
                li.appendChild(createListItem("OS Version", platform.os_version));
                platformsList.appendChild(li);
            });

            document.getElementById("size_value").textContent = data.target.size;
            document.getElementById("filetype_value").textContent = data.target.filetype;
            document.getElementById("media_type_value").textContent = data.target.media_type;
            document.getElementById("sha256_value").textContent = data.target.sha256;
            document.getElementById("sha1_value").textContent = data.target.sha1;
            document.getElementById("md5_value").textContent = data.target.md5;

            // Static Analysis
            document.getElementById("peid_signatures_value").textContent = data.static.pe.peid_signatures.join(", ");

            const peImportsList = document.getElementById("pe_imports_value");
            peImportsList.innerHTML = "";
            data.static.pe.pe_imports.forEach(importItem => {
                const importLi = createListItem("DLL", importItem.dll);
                importItem.imports.forEach(importItemDetail => {
                    importLi.appendChild(createListItem("Address", importItemDetail.address));
                    importLi.appendChild(createListItem("Name", importItemDetail.name));
                });
                peImportsList.appendChild(importLi);
            });

            document.getElementById("pe_exports_value").textContent = data.static.pe.pe_exports;

            const peSectionsList = document.getElementById("pe_sections_value");
            peSectionsList.innerHTML = "";
            data.static.pe.pe_sections.forEach(section => {
                const sectionLi = createListItem("Name", section.name);
                sectionLi.appendChild(createListItem("Virtual Address", section.virtual_address));
                sectionLi.appendChild(createListItem("Virtual Size", section.virtual_size));
                sectionLi.appendChild(createListItem("Size of Data", section.size_of_data));
                sectionLi.appendChild(createListItem("Entropy", section.entropy));
                peSectionsList.appendChild(sectionLi);
            });

            const peResourcesList = document.getElementById("pe_resources_value");
            peResourcesList.innerHTML = "";
            data.static.pe.pe_resources.forEach(resource => {
                const resourceLi = createListItem("Name", resource.name);
                resourceLi.appendChild(createListItem("Offset", resource.offset));
                resourceLi.appendChild(createListItem("Size", resource.size));
                resourceLi.appendChild(createListItem("Filetype", resource.filetype));
                resourceLi.appendChild(createListItem("Language", resource.language));
                resourceLi.appendChild(createListItem("Sublanguage", resource.sublanguage));
                peResourcesList.appendChild(resourceLi);
            });

            const peVersionInfoList = document.getElementById("pe_versioninfo_value");
            peVersionInfoList.innerHTML = "";
            data.static.pe.pe_versioninfo.forEach(versionInfo => {
                const versionInfoLi = createListItem("Name", versionInfo.name);
                versionInfoLi.appendChild(createListItem("Value", versionInfo.value));
                peVersionInfoList.appendChild(versionInfoLi);
            });

            document.getElementById("pe_imphash_value").textContent = data.static.pe.pe_imphash;
            document.getElementById("pe_timestamp_value").textContent = data.static.pe.pe_timestamp;
        }

        // Function to handle the change event of the analysis dropdown
        function handlePopulateCards() {
            const selectedId = analysisSelect.value;
            const selectedData = analysisData.find(item => item.id === selectedId);
            if (selectedData) {
                populateCardFields(selectedData);
            }
        }

        // Add event listeners for populating cards
        analysisSelect.addEventListener('change', handlePopulateCards);

        // Trigger initial population for the selected analysis (if any)
        handlePopulateCards();
    </script>
@endsection
