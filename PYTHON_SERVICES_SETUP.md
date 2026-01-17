# Python Services Setup Guide for BansalCRM2

## Overview
The Python microservice has been copied from migrationmanager2 and is now ready for setup in bansalcrm2.

## Quick Start

### 1. Install Python Dependencies

```bash
cd C:\xampp\htdocs\bansalcrm2\python_services
pip install -r requirements.txt
```

### 2. Configure Environment Variables

Add to `C:\xampp\htdocs\bansalcrm2\.env`:

```env
# Python Service Configuration
PYTHON_SERVICE_URL=http://localhost:5001
```

**Note**: The service runs on port **5001** (not 5000) to avoid conflicts with other services.

### 3. Start the Python Service

#### Windows:
```bash
cd C:\xampp\htdocs\bansalcrm2\python_services
start_services.bat
```

#### Or manually:
```bash
cd C:\xampp\htdocs\bansalcrm2\python_services
python main.py --host 127.0.0.1 --port 5001
```

### 4. Verify Service is Running

Open your browser or use curl:
```bash
curl http://localhost:5001/health
```

Expected response:
```json
{
  "status": "healthy",
  "services": {
    "pdf_service": "ready",
    "email_parser": "ready",
    "email_analyzer": "ready",
    "email_renderer": "ready",
    "docx_converter": "ready"
  }
}
```

## Service Endpoints

The Python service provides the following endpoints for the Email V2 system:

### Email Endpoints (used by Email V2)
- `POST /email/parse` - Parse .msg file
- `POST /email/analyze` - Analyze email content
- `POST /email/render` - Render email HTML
- `POST /email/parse-analyze-render` - Complete pipeline (recommended)

### Health Check
- `GET /health` - Service health status
- `GET /` - Service information

## Integration with Laravel

The Email V2 controllers (`EmailUploadV2Controller`) are already configured to use the Python service. They will:

1. Upload .msg files to the service
2. Receive parsed email data
3. Get AI-powered analysis (category, priority, sentiment)
4. Store enhanced HTML rendering

## Troubleshooting

### Service Won't Start

**Issue**: Port 5001 already in use
```bash
# Find process using port 5001
netstat -ano | findstr :5001

# Kill the process (replace PID with actual process ID)
taskkill /PID <PID> /F
```

**Issue**: Missing dependencies
```bash
cd C:\xampp\htdocs\bansalcrm2\python_services
pip install -r requirements.txt --upgrade
```

### Service Returns Errors

Check the logs:
```
C:\xampp\htdocs\bansalcrm2\python_services\logs\
```

Log files:
- `combined-YYYY-MM-DD.log` - All service logs
- `email_parser.log` - Email parsing specific
- `email_analyzer.log` - Analysis specific
- `email_renderer.log` - Rendering specific

### Test Service Independently

```bash
cd C:\xampp\htdocs\bansalcrm2\python_services
python test_service.py
```

This will test all endpoints and report any issues.

## Production Deployment

For production environments:

1. **Use a process manager** (e.g., supervisor, systemd, PM2)
2. **Configure firewall** to only allow localhost connections
3. **Enable logging** and monitor `logs/` directory
4. **Set appropriate permissions** on the python_services directory

### Windows Service (Production)

You can create a Windows service using NSSM (Non-Sucking Service Manager):

```bash
# Download NSSM from nssm.cc
nssm install BansalCRMPythonService "C:\Python\python.exe" "C:\xampp\htdocs\bansalcrm2\python_services\main.py --host 127.0.0.1 --port 5001"
nssm start BansalCRMPythonService
```

### Linux Service (Production)

For Linux servers, see `python_services/LINUX_START_HERE.md` for systemd setup.

## Security Notes

1. **Localhost Only**: The service should only be accessible from localhost (127.0.0.1)
2. **No External Access**: Do not expose port 5001 to the internet
3. **File Validation**: The service validates all uploaded files
4. **Temp File Cleanup**: Temporary files are automatically cleaned up

## Required Python Packages

All dependencies are in `requirements.txt`:

### Core Dependencies
- **FastAPI** - Web framework
- **uvicorn** - ASGI server
- **extract-msg** - Parse Outlook .msg files
- **beautifulsoup4** - HTML processing
- **PyPDF2** - PDF processing
- **Pillow** - Image processing

### Optional Dependencies
- **docx2pdf** - DOCX to PDF conversion (requires Microsoft Word on Windows)
- **LibreOffice** - Alternative DOCX converter (cross-platform)

## Performance Tips

1. **Keep Service Running**: Don't start/stop for each request
2. **Monitor Memory**: Email parsing can be memory-intensive for large files
3. **Limit File Sizes**: Configure max upload size in Laravel (default: 30MB)
4. **Use Queues**: For bulk email processing, use Laravel queues

## Next Steps

1. ✅ Python services copied
2. ⏳ Install Python dependencies (`pip install -r requirements.txt`)
3. ⏳ Add `PYTHON_SERVICE_URL` to `.env`
4. ⏳ Start the service (`start_services.bat`)
5. ⏳ Test the service (`python test_service.py`)
6. ⏳ Test Email V2 tab in client/partner detail pages

## Documentation

More detailed documentation is available in:
- `python_services/README.md` - General overview
- `python_services/QUICK_START.md` - Quick start guide
- `python_services/DEPLOYMENT_SUMMARY.md` - Deployment guide
- `python_services/PYTHON_SERVICES_START_HERE.md` - Complete documentation

---

**Status**: Python service files copied ✅
**Next**: Install dependencies and start service
