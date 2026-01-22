# Quick Reference Guide: Partner Agreements Feature

## How to Use the New Multiple Agreements Feature

### Viewing Agreements
1. Navigate to a Partner's detail page
2. Click on the **"Agreements"** tab
3. All agreements for this partner will be displayed in a table

### Adding a New Agreement
1. Click the **"+ Add Agreement"** button (top right of the Agreements tab)
2. Fill in the form fields:
   - **Contract Start Date** (required)
   - **Contract Expiry Date** (required)
   - **Commission Percentage** (optional)
   - **Bonus** (optional) - NEW FIELD
   - **Representing Regions** (optional - multi-select)
   - **Description** (optional) - NEW FIELD for notes
   - **GST** (checkbox)
   - **Default Super Agent** (optional)
   - **Status** (Active or Inactive)
   - **Document Upload** (optional - PDF, DOC, etc.)
3. Click **"Save Agreement"**
4. The agreement will appear in the table

### Editing an Agreement
1. In the agreements table, click the **Edit** button (pencil icon) for the agreement
2. The form will open with pre-filled data
3. Modify the fields as needed
4. Click **"Save Agreement"**

### Deleting an Agreement
1. In the agreements table, click the **Delete** button (trash icon)
2. Confirm the deletion
3. The agreement will be removed permanently

### Setting an Agreement as Active
- Only **one agreement can be active** at a time
- To activate an inactive agreement:
  1. Click the **Set Active** button (check icon) for the inactive agreement
  2. The previously active agreement will automatically become inactive
  3. The selected agreement becomes the active one

### Agreement Status Indicators
- **Green badge "Active"** - This is the currently active agreement
- **Gray badge "Inactive"** - This agreement is not currently active

### Viewing Agreement Documents
- Click the **"View"** link in the Document column
- The document will open in a new tab

### Understanding the Fields

#### Commission Percentage
The percentage commission rate for this agreement (e.g., 10.50 for 10.5%)

#### Bonus
A bonus amount in dollars (e.g., 500.00 for $500)

#### Description
Free-text field for:
- Special terms
- Notes about the agreement
- Conditions
- Any additional information

#### Representing Regions
Countries/regions this partner represents under this agreement

### Important Notes

1. **Active Agreement:** Only ONE agreement can be active at a time. The active agreement's data is used for:
   - Commission calculations
   - Invoice generation
   - Reports

2. **Backward Compatibility:** The system automatically syncs the active agreement data with the old partner fields. Existing features will continue to work.

3. **Document Storage:** All uploaded documents are stored securely in Amazon S3 cloud storage.

4. **Data Validation:**
   - Contract dates are required
   - All other fields are optional
   - You can save an agreement without a document

5. **Historical Records:** All agreements are preserved, even when inactive. This provides a complete history of partnership terms.

### Troubleshooting

**Agreements not loading?**
- Refresh the page
- Check your internet connection
- Check browser console for errors (F12)

**Can't upload document?**
- Check file size (max 10MB recommended)
- Ensure file format is supported (PDF, DOC, DOCX, etc.)
- Try again or contact support

**Can't set agreement as active?**
- Ensure the agreement exists and is currently inactive
- Only inactive agreements can be set as active

### For Developers

**API Endpoints:**
- GET `/partner/agreements/list` - List all agreements
- GET `/partner/agreement/get` - Get single agreement
- POST `/partner/agreement/store` - Create/Update agreement
- POST `/partner/agreement/delete` - Delete agreement
- POST `/partner/agreement/set-active` - Set as active

**Database Table:** `partner_agreements`

**Model:** `App\Models\PartnerAgreement`

**Controller:** `App\Http\Controllers\Admin\PartnersController`
