# Environment Variable Configuration for Email V2

## Add to .env file

Add the following line to your `C:\xampp\htdocs\bansalcrm2\.env` file:

```env
# Python Service Configuration (Email V2)
PYTHON_SERVICE_URL=http://localhost:5001
```

## Important Notes

1. **Port Number**: The service uses port **5001** (not 5000)
2. **Localhost Only**: Use `localhost` or `127.0.0.1` for security
3. **Service Must Be Running**: Start the Python service before testing Email V2

## To Add This Variable

### Method 1: Manual Edit
1. Open `C:\xampp\htdocs\bansalcrm2\.env` in a text editor
2. Add the line: `PYTHON_SERVICE_URL=http://localhost:5001`
3. Save the file

### Method 2: PowerShell Command
```powershell
Add-Content -Path "C:\xampp\htdocs\bansalcrm2\.env" -Value "`nPYTHON_SERVICE_URL=http://localhost:5001"
```

### Method 3: Echo Command
```bash
echo PYTHON_SERVICE_URL=http://localhost:5001 >> C:\xampp\htdocs\bansalcrm2\.env
```

## Verify Configuration

After adding the variable, clear Laravel's config cache:

```bash
cd C:\xampp\htdocs\bansalcrm2
php artisan config:clear
php artisan config:cache
```

## Check if Variable is Set

```bash
php artisan tinker
>>> env('PYTHON_SERVICE_URL')
=> "http://localhost:5001"
```

---

**Next Step**: Install Python dependencies and start the service
