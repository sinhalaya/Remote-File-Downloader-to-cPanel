# Remote File Downloader to cPanel

This project provides a simple PHP-based solution to download remote files directly to the server hosting the script (e.g., on cPanel). The script includes a graphical progress bar and percentage tracking to enhance the user experience. It supports downloading files to a specific folder on the server while displaying the progress in real-time.

## Features

- **Remote File Download**: Downloads files directly to the server.
- **Graphical Progress Bar**: Displays real-time progress with percentage tracking.
- **File Metadata Check**: Validates file URL and displays file name and size before downloading.
- **File Type Validation**: Supports specific file extensions only (`.zip`, `.xml`, `.json`, `.jsonl`, `.tar.gz`).
- **Dark-Themed UI**: Clean black background with white text for better readability.
- **Customizable Storage Path**: Saves files to a `downloads` directory.

## Installation Instructions

### 1. Upload to Server
Upload the `file_downloader.php` file to your cPanel or server using the File Manager or FTP.

### 2. Create the `downloads` Folder
- The script automatically creates a `downloads` directory in the same folder as the PHP script if it does not exist.
- Ensure the folder has write permissions (`0777`).

### 3. Access the Script
Open the script in your browser (e.g., `https://yourdomain.com/file_downloader.php`).

## How It Works

### Input URL:
- Enter the URL of the remote file you want to download in the input field.
- Supported file extensions: `.zip`, `.xml`, `.json`, `.jsonl`, `.tar.gz`.

### Check File:
- Click the **Check File** button to verify the URL and fetch metadata (file name and size).

### Download File:
- Once the file is verified, click the **Download to Server** button.
- The file is saved to the `downloads` directory on the server.

### Progress Tracking:
- A progress bar and percentage tracker update in real-time during the download process.

### Completion:
- When the download is complete, the progress bar shows **100% Complete**.

## Example Workflow

1. Enter the remote file URL (e.g., `https://example.com/sample.zip`).
2. Click **Check File** to validate the URL.
3. Click **Download to Server** to start the download.
4. Watch the progress bar update as the file downloads to the `downloads` directory.

---

## Remote File Download to cPanel (Simple SSH Solution)

To download files directly to cPanel using SSH, follow these steps:

### 1. Access SSH
- Use your SSH client (e.g., PuTTY) to connect to your server.
- Obtain your SSH credentials from your cPanel dashboard.

### 2. Navigate to the Target Directory
```bash
cd /path/to/target/directory
```

### 3. Use `wget` to Download the File
```bash
wget https://example.com/sample.zip
```

### 4. Verify the Download
Check the file in the directory:
```bash
ls -l
```

### 5. Set Permissions (Optional)
```bash
chmod 755 sample.zip
```

This method is useful for advanced users who want to bypass the web interface for larger files or faster downloads.

---

## Future Updates

The following features are planned for the next version:

- **Authentication**: Add a login system to restrict access.
- **Download History**: Log all downloaded files with metadata (URL, size, timestamp).
- **Multiple File Support**: Enable downloading multiple files at once.
- **File Management**: Add options to delete or move downloaded files from the server.
- **Customizable Storage Path**: Allow users to specify a custom folder for storing downloaded files.
- **Error Reporting**: Enhance error handling and display detailed messages for download issues.
- **Support for More File Types**: Extend support to additional file extensions.

---

## Troubleshooting

### Permission Issues
- Ensure the `downloads` directory has the correct permissions (`0777`).

### Unsupported URL
- Verify that the URL is valid and ends with one of the supported extensions.

### Progress Bar Not Updating
- Ensure PHP sessions are enabled on your server.

---

## License

This project is licensed under the MIT License. You are free to use, modify, and distribute this software.

---

## Contributing

Feel free to open an issue or submit a pull request for bug fixes or feature requests. Contributions are welcome!
