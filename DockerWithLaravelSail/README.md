# 🚀 Laravel Sail with WSL2 & Docker Setup Guide

## Introduction

**Docker** 🐳 is a lightweight virtualization tool that packages code and its dependencies into containers, making it easy to run and scale applications. **Laravel Sail** ⛵, introduced in December 2020, is a simple CLI tool for managing Docker environments tailored for Laravel projects. With Sail, you can seamlessly set up and manage your Laravel app using **PHP**, **MySQL**, and **Redis**—all without deep knowledge of Docker.

**WSL (Windows Subsystem for Linux)** 🐧 allows developers to run Linux environments directly on Windows, without the overhead of traditional virtual machines (VMs). **WSL 2** brings significant performance improvements over its predecessor, offering faster file I/O and more efficient integration.

---

## 📋 Prerequisites

### For Windows 11 / Latest Windows 10:
Simply run the following command in a terminal with admin privileges:
```bash
wsl --install
```
Restart your system, and WSL will be ready to use!

Alternatively, refer to this [Microsoft Support article](https://support.microsoft.com/en-us/topic/july-29-2021-kb5004296-os-builds-19041-1151-19042-1151-and-19043-1151-preview-6aba536a-6ed2-41cb-bc3d-3980e8693cc4) if needed.

---

## ⚙️ Manual Installation for Windows 10

1. **Enable WSL:**
   Run this command in a terminal with admin privileges:
   ```bash
   dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
   ```

2. **Enable Virtual Machine Feature:**
   ```bash
   dism.exe /online /enable-feature /featurename:VirtualMachinePlatform /all /norestart
   ```

3. **Download the Linux Kernel Update Package:**
   [Download here](https://wslstorestorage.blob.core.windows.net/wslblob/wsl_update_x64.msi).

4. **Set WSL 2 as Your Default Version:**
   ```bash
   wsl --set-default-version 2
   ```

5. **Install Ubuntu:**
   - Open Microsoft Store, search for **Ubuntu** 🐧, and install.
   - After installation, click **Launch**.
   - Set up your username and password for Ubuntu.

6. **Verify Installation:**
   ```bash
   wsl --list --verbose
   ```

---

## 🐋 Install Docker

1. **Install Docker Desktop:**
   [Download Docker](https://docs.docker.com/desktop/windows/install/).

2. **Enable WSL 2 Integration:**
   During installation, Docker will prompt you to enable **WSL 2**. After installation, ensure that **WSL 2** is enabled from **Settings > General**.

3. **Configure WSL Integration:**
   - Go to **Settings > Resources > WSL Integration**.
   - Turn on Ubuntu (or any Linux distribution you're using).

4. **Install PowerShell** from the [Microsoft Store](https://www.microsoft.com/en-us/powershell).

---

## ⛵ Let's Sail with Laravel

1. Open **PowerShell** and select **Ubuntu** from the menu.
2. Navigate to your directory:
   ```bash
   ls
   ```
3. Create a new folder:
   ```bash
   mkdir foldername
   cd foldername
   ```

4. Install Laravel using the following command:
   ```bash
   curl -s https://laravel.build/newapp | bash
   ```

5. Navigate to the new Laravel directory:
   ```bash
   cd newapp
   ```

6. Start Laravel Sail:
   ```bash
   ./vendor/bin/sail up
   ```

If you face issues, ensure your antivirus software isn't blocking Docker files. Exclude the directory: 
`C:\ProgramData\DockerDesktop\vm-data\`.

---

## 🌐 Running the Project

Once the Sail container is up and running, access your Laravel project in your browser at:
```bash
http://localhost
```

---

## 🛠️ Daily Sail Commands

- **Start Sail in detached mode**:
   ```bash
   ./vendor/bin/sail up -d
   ```

- **Stop Sail**:
  - Option 1: Use `Ctrl + C` in the terminal.
  - Option 2: Run the command:
    ```bash
    ./vendor/bin/sail down
    ```

---

## ⚡ Helpful Alias for Sail

You can simplify running Sail commands by creating a Bash alias:
```bash
alias sail='bash vendor/bin/sail'
```

Now you can start Sail with just:
```bash
sail up
```

---

## 🖥️ Visual Studio Code Integration

Make sure you are in the correct project directory, then run:
```bash
code .
```

---

## 🛢️ Connect with SQL Management Tools

To connect with tools like **HeidiSQL**, use the following credentials:

- **Host**: `localhost`
- **Username**: `sail`
- **Password**: `password`

---

## 🔧 Troubleshooting

- **Performance Issues**: Ensure that **WSL 2** and **Docker** are correctly configured.
- **Antivirus Conflicts**: Make sure to exclude Docker directories in your antivirus settings to avoid conflicts.

---

## 👥 Community & Support

If you encounter any issues, feel free to open a discussion or issue in this repository, or check out the official documentation for [Laravel Sail](https://laravel.com/docs/8.x/sail) and [Docker](https://docs.docker.com/desktop/windows/install/).
