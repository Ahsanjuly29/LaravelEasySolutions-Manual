Laravel Sail (WSL2) With Docker.
Docker is the light weight virtual machine. It packages up code and all dependencies as a “container”.
Laravel Sail was introduced in 2020, Dec. Laravel Sail is a light-weight command-line interface for interacting with Laravel’s default Docker development environment. Sail provides a great starting point for building a Laravel application using PHP, MySQL, and Redis without requiring prior Docker experience
WSL stands for Windows Subsystem for Linux, it allows developers to run Linux environments directly on Windows without any “real” hosted VM. WSL 2 is a major overhaul and provides the benefits of WSL 1 with better file IO performance (20x faster). It is not like the traditional VM which may be slow to boot up, large resource overhead, and fully isolated.
1) Simplified Install (Windows 11 / Windows 10 latest)
On command window with administrator privileges, run
“wsl –install”
With a restart, WSL will be ready to Use.
if not use this link 
https://support.microsoft.com/en-us/topic/july-29-2021-kb5004296-os-builds-19041-1151-19042-1151-and-19043-1151-preview-6aba536a-6ed2-41cb-bc3d-3980e8693cc4
or 
2) Manual Install(Windows 10)
1.	Enable WSL
Open a command window with administrator privileges then run
“ dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart ”
2.	Enable Virtual Machine feature
WSL 2: dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
3.	Download the Linux kernel update package
https://wslstorestorage.blob.core.windows.net/wslblob/wsl_update_x64.msi
4.	Set WSL 2 as your default version
“ wsl --set-default-version 2 “
5.	Install Ubuntu
o	Open the Microsoft Store and then search & select Ubuntu.
o	From the distribution’s page, select “Get” then select “Install”
o	Click “Launch” when it is ready
o	Create your username and password for your Ubuntu
6.	Use “ wsl --list –verbose ” to verify your installation

1.	Install Docker
https://docs.docker.com/desktop/windows/install/
2.	Install
Follow the installation wizard to install the Docker Desktop. If your WSL 2 is configured properly, it should prompt you to enable WSL 2 during installation.
                                                if notification comes install 
             In Docker menu, select Settings > General : Make sure WSL 2 is enabled…... 
 
3.	Configure WSL Integration
Turn on Ubuntu/Linux from(select Settings > Resources > WSL Integration)
 
4.	Install PowerShell from Microsoft Store.
Lets Sail
1.	Open Powershell:
see a option menu click here and select ubuntu.
2.	Press “ls” to see directory
3.	Create Folder there ”mkdir foldername”
4.	Enter into folder by “cd foldername”
5.	Install Laravel using “curl -s https://laravel.build/newapp | bash”
6.	After that enter to that directory using “cd newapp”. If you are not sure about your directory type “ls”to see directories you have.
7.	Then Type “./vendor/bin/sail up”

If you encounter any issue, your Antivirus software might be the culprit. Please make the exclusion for the path “C:\ProgramData\DockerDesktop\vm-data\” .
 
Run Project on Chrome(any browser):
Once the container has been started, you can go to http://localhost for accessing your brand new Laravel project.

Daily Sail routine
1.	Start Sail in detached mode
“ ./vendor/bin/sail up -d ”
2.	Stop Sail in 2 methods
i.	“ Ctrl+C ”
ii.	“./vendor/bin/sail down”

Alias
Instead of typing a long path name, configure a Bash alias that allows you to call sail directly
“ alias sail='bash vendor/bin/sail' ”
Visual Studio Code
Make sure you are in your project from command promt(powershell). Then run Code .

Connect with your SQL Management Software(like. heidiSQL) using localhost with the default username is: ‘sail’ and password is: ‘password’.
