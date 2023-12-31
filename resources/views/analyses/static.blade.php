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
                            <li><strong>Analysis ID:</strong> <span id="analysis_id_value"></span></li>
                            <li><strong>Score:</strong> <span id="score_value"></span></li>
                            <li><strong>Category:</strong> <span id="category_value"></span></li>
                            <li><strong>Target:</strong>
                                <ul>
                                    <li><strong>Filename:</strong> <span id="filename_value"></span></li>
                                    <li><strong>Original Filename:</strong> <span id="orig_filename_value"></span></li>
                                    <li><strong>Platforms:</strong>
                                        <ul id="platforms_value"></ul>
                                    </li>
                                    <li><strong>Size:</strong> <span id="size_value"></span></li>
                                    <li><strong>Filetype:</strong> <span id="filetype_value"></span></li>
                                    <li><strong>Media Type:</strong> <span id="media_type_value"></span></li>
                                    <li><strong>SHA256:</strong> <span id="sha256_value"></span></li>
                                    <li><strong>SHA1:</strong> <span id="sha1_value"></span></li>
                                    <li><strong>MD5:</strong> <span id="md5_value"></span></li>
                                </ul>
                            </li>
                            <li><strong>Static Analysis:</strong>
                                <ul>
                                    <li><strong>PEID Signatures:</strong> <span id="peid_signatures_value"></span></li>
                                    <li><strong>PE Imports:</strong>
                                        <ul id="pe_imports_value"></ul>
                                    </li>
                                    <li><strong>PE Exports:</strong> <span id="pe_exports_value"></span></li>
                                    <li><strong>PE Sections:</strong>
                                        <ul id="pe_sections_value"></ul>
                                    </li>
                                    <li><strong>PE Resources:</strong>
                                        <ul id="pe_resources_value"></ul>
                                    </li>
                                    <li><strong>PE Version Info:</strong>
                                        <ul id="pe_versioninfo_value"></ul>
                                    </li>
                                    <li><strong>PE Imphash:</strong> <span id="pe_imphash_value"></span></li>
                                    <li><strong>PE Timestamp:</strong> <span id="pe_timestamp_value"></span></li>
                                </ul>
                            </li>
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
        const populateBtn = document.getElementById('populateBtn');

        function populateAnalysisDropdown() {
            // Add options to the analysis select dropdown
            analysisData.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.analysis_id; // You can change this to display the appropriate text
                analysisSelect.appendChild(option);
            });
        }

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
                const li = document.createElement("li");
                li.textContent = platform.platform;
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
                const importLi = document.createElement("li");
                importLi.innerHTML = `<strong>DLL:</strong> ${importItem.dll}<br>`;
                importItem.imports.forEach(importItemDetail => {
                    importLi.innerHTML += `<strong>Address:</strong> ${importItemDetail.address}<br>`;
                    importLi.innerHTML += `<strong>Name:</strong> ${importItemDetail.name}<br>`;
                });
                peImportsList.appendChild(importLi);
            });

            document.getElementById("pe_exports_value").textContent = data.static.pe.pe_exports;

            const peSectionsList = document.getElementById("pe_sections_value");
            peSectionsList.innerHTML = "";
            data.static.pe.pe_sections.forEach(section => {
                const sectionLi = document.createElement("li");
                sectionLi.innerHTML = `<strong>Name:</strong> ${section.name}<br>`;
                sectionLi.innerHTML += `<strong>Virtual Address:</strong> ${section.virtual_address}<br>`;
                sectionLi.innerHTML += `<strong>Virtual Size:</strong> ${section.virtual_size}<br>`;
                sectionLi.innerHTML += `<strong>Size of Data:</strong> ${section.size_of_data}<br>`;
                sectionLi.innerHTML += `<strong>Entropy:</strong> ${section.entropy}<br>`;
                peSectionsList.appendChild(sectionLi);
            });

            const peResourcesList = document.getElementById("pe_resources_value");
            peResourcesList.innerHTML = "";
            data.static.pe.pe_resources.forEach(resource => {
                const resourceLi = document.createElement("li");
                resourceLi.innerHTML = `<strong>Name:</strong> ${resource.name}<br>`;
                resourceLi.innerHTML += `<strong>Offset:</strong> ${resource.offset}<br>`;
                resourceLi.innerHTML += `<strong>Size:</strong> ${resource.size}<br>`;
                resourceLi.innerHTML += `<strong>Filetype:</strong> ${resource.filetype}<br>`;
                resourceLi.innerHTML += `<strong>Language:</strong> ${resource.language}<br>`;
                resourceLi.innerHTML += `<strong>Sublanguage:</strong> ${resource.sublanguage}<br>`;
                peResourcesList.appendChild(resourceLi);
            });

            const peVersionInfoList = document.getElementById("pe_versioninfo_value");
            peVersionInfoList.innerHTML = "";
            data.static.pe.pe_versioninfo.forEach(versionInfo => {
                const versionInfoLi = document.createElement("li");
                versionInfoLi.innerHTML = `<strong>Name:</strong> ${versionInfo.name}<br>`;
                versionInfoLi.innerHTML += `<strong>Value:</strong> ${versionInfo.value}<br>`;
                peVersionInfoList.appendChild(versionInfoLi);
            });

            document.getElementById("pe_imphash_value").textContent = data.static.pe.pe_imphash;
            document.getElementById("pe_timestamp_value").textContent = data.static.pe.pe_timestamp;
        }

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



        function handlePopulateCards() {
            const selectedId = analysisSelect.value;
            const selectedData = analysisData.find(item => item.id === selectedId);
            if (selectedData) {
                populateCardFields(selectedData);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            populateAnalysisDropdown();
            analysisSelect.addEventListener('change', handlePopulateCards)
        });


        // Call the function to populate the card fields with data for the selected analysis
        analysisSelect.addEventListener('change', function () {
            const selectedId = this.value;
            const selectedData = analysisData.find(item => item.id === selectedId);
            if (selectedData) {
                populateCardFields(selectedData);
            }
        });

        // Populate card fields with data for the initial selected analysis (if any)
        if (analysisSelect.value) {
            const initialSelectedData = analysisData.find(item => item.id === analysisSelect.value);
            if (initialSelectedData) {
                populateCardFields(initialSelectedData);
            }
        }
    </script>

@endsection
