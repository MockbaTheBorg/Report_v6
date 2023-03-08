# Report_v6 - Report generator based on Google Charts

### This report generator reads a Report Definition File in either JSON or YAML formats and generates a HTML page which can then be converted to PDF.<br>

__Usage:__ ```./report.php [-h|-help] [-debug[=n]] [-report=<name>[.json|.yaml]] [-output=<name>[.htm]```<br>

The Report Definition File defines a sequence of pages and the objects to be displayed inside those pages.<br>
The objects can be of the following types:<bt>
* __image__ - Image file to be displayed inside the page<br>
* __text__ - Text to be displayed inside the page.<br>
  Can be defined directly inside the file or read from a .txt file.<br>
* __chart__ - Google chart to be inserted at the page.<br>
  Can be generated directly from data inside the Report Definition File, sourced from a CSV file or from a SQLite3 database query.<br>
* __variable__ - Variable to be set directly or from a SQLite3 database query.<br>
* __dummy__ - Used to execute commands or SQLite3 queries without generating an object inside the page.<br>
* __index__ - Automatically generates a content index of the report pages.<br>

### To enable chromium on WSL2, as root do:<br>
* Edit /etc/wsl.conf and add to it:
```
  [boot]
  systemd=true
```
* Execute ```wsl --shutdown``` from PowerShell
* Start WSL again
* As root execute: ```snap install chromium```

