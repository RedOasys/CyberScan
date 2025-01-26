
# CyberScanosis - The Art of Malware Analysis

CyberScanosis is a comprehensive Malware Analysis Toolkit developed as a part of a thesis project at Bahrain Polytechnic. This toolkit uniquely integrates static and dynamic analysis methods for malware detection and management. It aims to address the challenges in cybersecurity by providing a reliable solution for identifying and analyzing malware threats in various operational environments.


## Connecting to the Deployed Environment

To connect to the deployed environment, follow these steps:

1. **Obtain the Private Key:**
   - Locate the private key file (.pem or .ppk) that was provided within the zip file.

2. **Download an SSH Client:**
   - If you don't have one already, download an SSH client like PuTTY or Git Bash.

3. **Connect to the SSH Host:**
   - Open your SSH client and enter the following information:
     - Host Name: `cyberscanosis.live`
     - Port: `22` (default for SSH)
     - Private Key File: `Select the path to your private key file.`

4. **Authenticate with the Key:**
   - Connect to the deployed environment using the key.

5. **Access the Shell:**
   - Once connected, you'll have a command-line interface to interact with the deployed environment.

**Important Notes:**

- **Private Key Security:** Keep the private key file secure and confidential. Do not share it with unauthorized individuals.
- **Firewall Restrictions:** Ensure that your local firewall allows SSH connections on port 22.
- **Troubleshooting:** If you encounter issues connecting, double-check the host name, port number, and private key file. Verify that the SSH service is running on the server.



## Cuckoo3 Installation Guide

# Installation Guide for Cuckoo 3

This guide provides step-by-step instructions for installing Cuckoo 3 on an Ubuntu Server 20.04 virtual machine within Vmware Workstation. Cuckoo is a popular open-source malware analysis platform.

This guide follows the following official documentation: https://cuckoo-hatch.cert.ee/static/docs/introduction/cuckoo/

## Installation

1. **Download Ubuntu Server 20.04**
   - Obtain the Ubuntu Server 20.04 ISO image from the official website.

2. **Import the Ubuntu Image into Vmware Workstation**
   - Import the Ubuntu image into Vmware Workstation with the following specifications (these may vary):
     - 8 CPUs
     - 16 GB Memory
     - 120 GB storage (Thin Provision)

3. **Enable Nested Virtualization**
   - Since you'll be running a VM inside a VM, enable nested virtualization.
   - Go to the "CPU" section and check the box for "Expose hardware assisted virtualization to the guest OS."

4. **During the Installation**
   - Optionally set the hostname to `cuckoo-sandbox`.
   - Create a non-root user named `cuckoo`.
   - Enable the `Open-SSH server` for remote access.

## Dependency Installation

1. **Update the System**
   - Run the following command to update the system:
     ```shell
     sudo apt update && sudo apt upgrade -y
     ```

2. **Install Required Dependencies**
   - Install the following dependencies for a successful installation:
     ```shell
     sudo apt install git build-essential python3-dev python3.8-venv libhyperscan5 libhyperscan-dev libjpeg8-dev zlib1g-dev unzip p7zip-full rar unace-nonfree cabextract yara tcpdump genisoimage qemu-system-x86 qemu-utils qemu-system-common -y
     ```

3. **Fix Permissions for KVM Access**
   - Add the `cuckoo` user to the `kvm` group:
     ```shell
     sudo adduser cuckoo kvm
     sudo chmod 666 /dev/kvm
     ```

4. **Configuring `tcpdump`**
   - Add the existing user `cuckoo` to the `pcap` group:
     ```shell
     sudo groupadd pcap
     sudo adduser cuckoo pcap
     sudo chgrp pcap /usr/sbin/tcpdump
     ```

   - Allow creation of pcaps for non-root users:
     ```shell
     sudo setcap cap_net_raw,cap_net_admin=eip /usr/sbin/tcpdump
     ```

   - Disable the Apparmor profile for tcpdump:
     ```shell
     sudo ln -s /etc/apparmor.d/usr.sbin.tcpdump /etc/apparmor.d/disable/
     sudo apparmor_parser -R /etc/apparmor.d/disable/usr.sbin.tcpdump
     ```

   - Reload the Apparmor profile:
     ```shell
     sudo apparmor_parser -r /etc/apparmor.d/usr.sbin.tcpdump
     ```

## Installing Cuckoo from Source

1. **Fetch the Source from GitHub**
   - Change ownership to `cuckoo` and clone the Cuckoo source code:
     ```shell
     sudo chown cuckoo /opt && cd /opt
     git clone https://github.com/cert-ee/cuckoo3
     cd cuckoo3
     ```

2. **Create and Activate a Python Environment for Cuckoo**
   - Ensure the `wheel` package is installed:
     ```shell
     python3 -m venv venv
     source venv/bin/activate
     pip install wheel
     ```

3. **Launch the Main Installer Script**
   - Execute the installer script:
     ```shell
     ./install.sh
     ```

4. **Create Cuckoo Working Directory (CWD)**
   - Create the CWD:
     ```shell
     cuckoo createcwd
     ```

5. **Install the Stager and Monitoring Binaries**
   - Install the stager and monitoring binaries:
     ```shell
     cuckoo getmonitor monitor.zip
     ```

6. **Extract Cuckoo Signatures**
   - Unzip the Cuckoo signatures to the correct location:
     ```shell
     unzip signatures.zip -d ~/.cuckoocwd/signatures/cuckoo/
     ```

## Installing `vmcloak`

1. **Install `vmcloak` (Optional)**
   - While not mandatory, `vmcloak` simplifies VM creation and configuration:
     ```shell
     git clone https://github.com/hatching/vmcloak.git && cd vmcloak
     pip install .
     cd ..
     ```

## Configuring QEMU Network Interface

1. **Create a New Bridge (`br0`)**
   - Create a new bridge named `br0` with IP range `192.168.30.1/24`:
     ```shell
     sudo /opt/cuckoo3/venv/bin/vmcloak-qemubridge br0 192.168.30.1/24
     ```

2. **Append `allow br0` to `bridge.conf`**
   - Add the following line to the `bridge.conf` file:
     ```shell
     sudo mkdir -p /etc/qemu
     echo 'allow br0' | sudo tee /etc/qemu/bridge.conf
     ```

3. **Change Permissions for `qemu-bridge-helper`**
   - Set special SUID permissions for `qemu-bridge-helper` script:
     ```shell
     sudo chmod u+s /usr/lib/qemu/qemu-bridge-helper
     ```

## VM Setup (Windows 10)

1. **Download Windows 10 ISO (build 1703)**
   - Download the Windows 10 ISO from the official Microsoft website.

2. **Create a VM with `vmcloak`**
   - Use `vmcloak` to create a Windows 10 VM:
     ```shell
     sudo /opt/cuckoo3/venv/bin/vmcloak install win10x64 --iso /path/to/windows10.iso
     ```

3. **Install Windows 10**
   - Install Windows 10 in the VM using the `vmcloak` script.

4. **Snapshot the VM**
   - Create a snapshot of the VM to revert to a clean state after analysis:
     ```shell
     virsh snapshot-create-as cuckoo-win10 snapshot-1 "Initial Snapshot"
     ```

## Starting Cuckoo

1. **Start Cuckoo**
   - Start the Cuckoo sandbox with the following command:
     ```shell
     cuckoo
     ```

2. **Access the Web Interface**
   - Access the Cuckoo web interface at `http://<your-server-ip>:8000`.

## Submitting Samples
   - You can now submit malware samples for analysis through the Cuckoo web interface.

## Starting the API

- cd into the Cuckoo Directory

     ```shell
     cd /opt/cuckoo3
     ```
- Activate the Venv

     ```shell
     source venv/bin/activate
     ```
- Start the API

     ```shell
     cuckoo api -h <The IP address to serve the API on> -p <The Port to use>
     ```
    

This guide provides a basic setup for Cuckoo 3. Please refer to the official documentation for more advanced configurations and usage.

        
                                    
## Local Development Installation
Ensure that your system meets the following requirements:

- Latest Version of Composer found here: https://getcomposer.org/.
- Cuckoo3 Sandbox, follow the above guide or for advanced users use the following repository : https://github.com/cert-ee/cuckoo3/tree/main
- XAMPP (PHP 8.2+): https://www.apachefriends.org/
- git : https://git-scm.com/


Make sure your php.ini file has zip enabled like so:
```bash
;extension=soap
;extension=sockets
;extension=sodium
;extension=sqlite3
;extension=tidy
;extension=xsl
extension=zip
```

Clone the repository from GitHub.
```bash
git clone https://[PAT_TOKEN]@github.com/RedOasys/MalSys.git
```


cd into the cloned folder.
```bash
cd MalSys
```

Replace the contents of the .env file with the contents of the attached .env that was uploaded in the submission zip file. (The below is an example and is missing important details for security purposes):
```bash
APP_NAME=CyberScanosis
APP_ENV=local
APP_KEY=
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

CUCKOO_API_BASE_URL=
CUCKOO_API_TOKEN=

VT_API_KEY=

cymru_user=
cymru_password=

```

Using a command line tool such as cmd or terminal execute the following commands after using cd to change the directory to the cloned directory 'MalSys'.

-  Installing dependencies for project.
```bash
composer install
```
- Creating project key.
```bash
php artisan key:generate
```
- Performing Database Migrations.
```bash
php artisan migrate
```
- Serving the Application on localhost.
```bash
php artisan serve
```
- The Application should now be accessable via http://localhost:8000

## MalSys Azure Deployment Guide

This guide outlines the steps to deploy the MalSys website on an Azure virtual machine running Debian 11.

## Prerequisites

- An Azure account with an active subscription.
- A Debian 11 virtual machine on Azure.
- A PEM key file for SSH access to the VM.
- Basic familiarity with Linux command-line and Git.

## Steps

1. **Set up Azure VM and Security:**
   - Create a new Azure virtual machine with Debian 11.
   - Generate a new PEM key file for SSH access.
   - Create inbound rules for SSH (port 22), MySQL (port 3306), HTTP (port 80), and HTTPS (port 443).

2. **Connect to VM:**
   - Use an SSH client to connect to the VM using the public DNS generated on Azure and the PEM key file.

3. **Update and Install Software:**
   - Become root user: `sudo su`
   - Update package list: `apt-get update -y`
   - Install Git: `apt install git -y`
   - Configure Git user details: `git config --global user.name "your_name" && git config --global user.email "your_email@example.com"`
   - Install Apache2 web server: `apt install apache2`
   - Start Apache2 service: `systemctl start apache2`
   - Install PHP 8.2 and related modules:
     ```bash
     apt-get install ca-certificates apt-transport-https software-properties-common -y
     echo "deb [https://packages.sury.org/php/](https://packages.sury.org/php/) $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/sury-php.list
     wget -qO - [https://packages.sury.org/php/apt.gpg](https://packages.sury.org/php/apt.gpg) | apt-key add -
     apt-get update -y
     apt-get install php8.2 libapache2-mod-php php8.2-dev php8.2-zip php8.2-curl php8.2-mbstring php8.2-mysql php8.2-gd php8.2-xml
     ```
   - Install MariaDB server and secure installation:
     ```bash
     apt install mariadb-server
     mysql_secure_installation
     # (Answer prompts as instructed in the guide)
     ```

4. **Set up Database:**
   - Connect to MariaDB: `mysql`
   - Create database and user:
     ```sql
     CREATE DATABASE malsys;
     CREATE USER 'malsys'@'127.0.0.1' IDENTIFIED BY 'password';
     GRANT ALL PRIVILEGES ON malsys.* TO malsys@127.0.0.1;
     FLUSH PRIVILEGES;
     exit
     ```

5. **Install Composer:**
   - Install Composer: `curl -sS https://getcomposer.org/installer | php`
   - Move Composer to /usr/local/bin: `mv composer.phar /usr/local/bin/composer`
   - Make Composer executable: `chmod +x /usr/local/bin/composer`

6. **Clone Project:**
   - Navigate to web directory: `cd /var/www/html`
   - Clone project from GitHub: `git clone https://[PAT_TOKEN]@github.com/RedOasys/MalSys.git`
   - Enter project directory: `cd MalSys`

7. **Configure Environment:**
   - Rename .env file: `mv .env.example .env`
   - Edit .env file (use the values that are present within the submission zip file): `nano .env`

8. **Install Dependencies and Set Up:**
   - Install dependencies: `composer install`
   - Generate application key: `php artisan key:generate`
   - Run migrations: `php artisan migrate`

9. **Configure Apache Virtual Host:**
   - Create virtual host file: `nano /etc/apache2/sites-available/MalSys.conf`
   - Paste virtual host configuration (replace ServerName with Azure DNS name). 
    ```bash
    <VirtualHost *:80>
    ServerAdmin admin@example.com
    ServerName <azure dns name or IP address or custom domain>
    DocumentRoot /var/www/html/MalSys/public

    <Directory /var/www/html/MalSys>
    Options Indexes MultiViews FollowSymLinks
    AllowOverride All
    Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>
   ```
    Save and exit.

10. **Enable Virtual Host and Permissions:**
   - Enable rewrite module: `sudo a2enmod rewrite`
   - Enable virtual host: `a2ensite MalSys.conf`
   - Restart Apache: `systemctl restart apache2`
   - Set ownership and permissions:
     ```bash
     cd /var/www/html/MalSys
     chown -R www-data:www-data /var/www/html/MalSys
     chmod -R 775 /var/www/html/MalSys
     chmod -R 775 /var/www/html/MalSys/storage
     chmod -R 775 /var/www/html/MalSys/bootstrap/cache
     ```

11. **Access the Website:**
   - Try Accessing the website using the domain used in apache configuration or the azure VM IP address or the custom azure DNS.
   - The Website should be working, if not consult the documentation above and try again. 
   

   
## Appendix

For detailed design, implementation, and user manuals, please refer to the Appendices section in the thesis document.


## Contributing

Fork the repository.

Create your feature branch (git checkout -b feature/AmazingFeature).

Commit your changes (git commit -m 'Add some AmazingFeature').

Push to the branch (git push origin feature/AmazingFeature).

Open a pull request.





## Acknowledgements


 - [Cuckoo3 Hatch Cert-EE](https://cuckoo-hatch.cert.ee/static/docs/introduction/cuckoo/)
 - [ProxMox and Cuckoo](https://4d5a.re/proxmox-cuckoo-a-powerful-combo-for-your-home-malware-lab/)
 - [Cuckoo3 Installation Guide](https://reversingfun.com/posts/cuckoo-3-installation-guide/)
- [Deploy Laravel App to Production](https://mytechjourne.hashnode.dev/how-i-deployed-laravel-realworld-example-app-on-debian-11)



