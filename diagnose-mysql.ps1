# MySQL Diagnostic Script for XAMPP
# Run this script as Administrator

Write-Host "=== MySQL/XAMPP Diagnostic Tool ===" -ForegroundColor Cyan
Write-Host ""

# Check if running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "WARNING: Not running as Administrator. Some checks may fail." -ForegroundColor Yellow
    Write-Host ""
}

# 1. Check Port 3306
Write-Host "1. Checking Port 3306..." -ForegroundColor Yellow
$port3306 = netstat -ano | findstr :3306
if ($port3306) {
    Write-Host "   [ISSUE FOUND] Port 3306 is in use:" -ForegroundColor Red
    Write-Host $port3306
    Write-Host ""
    Write-Host "   To kill the process, run:" -ForegroundColor Yellow
    $pid = ($port3306 -split '\s+')[-1]
    Write-Host "   taskkill /PID $pid /F" -ForegroundColor Cyan
} else {
    Write-Host "   [OK] Port 3306 is available" -ForegroundColor Green
}
Write-Host ""

# 2. Check MySQL Processes
Write-Host "2. Checking MySQL Processes..." -ForegroundColor Yellow
$mysqlProcesses = Get-Process | Where-Object {$_.ProcessName -like "*mysql*"}
if ($mysqlProcesses) {
    Write-Host "   [FOUND] MySQL processes running:" -ForegroundColor Yellow
    $mysqlProcesses | Format-Table ProcessName, Id, CPU -AutoSize
} else {
    Write-Host "   [OK] No MySQL processes found" -ForegroundColor Green
}
Write-Host ""

# 3. Check XAMPP Installation
Write-Host "3. Checking XAMPP Installation..." -ForegroundColor Yellow
$xamppPath = "C:\xampp"
if (Test-Path $xamppPath) {
    Write-Host "   [OK] XAMPP found at: $xamppPath" -ForegroundColor Green
    
    # Check MySQL data directory
    $mysqlDataPath = "$xamppPath\mysql\data"
    if (Test-Path $mysqlDataPath) {
        Write-Host "   [OK] MySQL data directory found" -ForegroundColor Green
        
        # Check for critical files
        $criticalFiles = @("ibdata1", "ib_logfile0", "ib_logfile1")
        foreach ($file in $criticalFiles) {
            $filePath = "$mysqlDataPath\$file"
            if (Test-Path $filePath) {
                $fileSize = (Get-Item $filePath).Length / 1MB
                Write-Host "   [OK] $file exists ($([math]::Round($fileSize, 2)) MB)" -ForegroundColor Green
            } else {
                Write-Host "   [ISSUE] $file is MISSING!" -ForegroundColor Red
            }
        }
    } else {
        Write-Host "   [ISSUE] MySQL data directory not found!" -ForegroundColor Red
    }
    
    # Check for error logs
    $errorLogs = Get-ChildItem "$mysqlDataPath\*.err" -ErrorAction SilentlyContinue
    if ($errorLogs) {
        Write-Host "   [FOUND] Error log files:" -ForegroundColor Yellow
        foreach ($log in $errorLogs) {
            Write-Host "   - $($log.Name)" -ForegroundColor Yellow
            Write-Host "     Last 5 lines:" -ForegroundColor Gray
            Get-Content $log.FullName -Tail 5 | ForEach-Object {
                Write-Host "     $_" -ForegroundColor Gray
            }
        }
    }
} else {
    Write-Host "   [ISSUE] XAMPP not found at default location!" -ForegroundColor Red
}
Write-Host ""

# 4. Check Disk Space
Write-Host "4. Checking Disk Space..." -ForegroundColor Yellow
$drive = Get-PSDrive C
$freeSpaceGB = [math]::Round($drive.Free / 1GB, 2)
$usedSpaceGB = [math]::Round(($drive.Used / 1GB), 2)
Write-Host "   Free Space: $freeSpaceGB GB" -ForegroundColor $(if ($freeSpaceGB -lt 1) { "Red" } else { "Green" })
Write-Host "   Used Space: $usedSpaceGB GB" -ForegroundColor Gray
if ($freeSpaceGB -lt 1) {
    Write-Host "   [WARNING] Low disk space may cause MySQL issues!" -ForegroundColor Red
}
Write-Host ""

# 5. Check Windows Services
Write-Host "5. Checking MySQL Windows Service..." -ForegroundColor Yellow
$mysqlService = Get-Service -Name "*mysql*" -ErrorAction SilentlyContinue
if ($mysqlService) {
    Write-Host "   [FOUND] MySQL service:" -ForegroundColor Yellow
    $mysqlService | Format-Table Name, Status, StartType -AutoSize
} else {
    Write-Host "   [INFO] No MySQL Windows service found (XAMPP uses its own service)" -ForegroundColor Gray
}
Write-Host ""

# 6. Check Recent Windows Event Logs
Write-Host "6. Checking Recent Application Event Logs..." -ForegroundColor Yellow
try {
    $events = Get-WinEvent -LogName Application -MaxEvents 10 -ErrorAction SilentlyContinue | 
        Where-Object { $_.Message -like "*mysql*" -or $_.Message -like "*xampp*" }
    if ($events) {
        Write-Host "   [FOUND] Recent MySQL/XAMPP related events:" -ForegroundColor Yellow
        $events | ForEach-Object {
            Write-Host "   [$($_.TimeCreated)] $($_.LevelDisplayName): $($_.Message.Substring(0, [Math]::Min(100, $_.Message.Length)))..." -ForegroundColor Gray
        }
    } else {
        Write-Host "   [OK] No recent MySQL/XAMPP events found" -ForegroundColor Green
    }
} catch {
    Write-Host "   [INFO] Could not access Event Logs (may need Administrator)" -ForegroundColor Gray
}
Write-Host ""

# Summary
Write-Host "=== Diagnostic Complete ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Review the issues above" -ForegroundColor White
Write-Host "2. Check MYSQL_TROUBLESHOOTING.md for solutions" -ForegroundColor White
Write-Host "3. Check XAMPP MySQL error logs at: C:\xampp\mysql\data\*.err" -ForegroundColor White
Write-Host "4. Try stopping and starting MySQL from XAMPP Control Panel" -ForegroundColor White
Write-Host ""

