# 📥 Upload .MSI File to Supabase Storage
**Time Required:** 10-15 minutes  
**Difficulty:** Easy

---

## 🎯 What This Does

Uploads your AutoCAD plugin (.msi installer) to Supabase Storage so users can download it after purchasing a license.

---

## 📋 STEP 1: Access Supabase Storage (2 minutes)

1. **Login to Supabase:**
   - Go to: https://supabase.com/dashboard
   - Login with your account

2. **Select Your Project:**
   - Click on your UL-NEC project

3. **Go to Storage:**
   - Left sidebar → Click **Storage**

---

## 📋 STEP 2: Create Storage Bucket (3 minutes)

If not already created:

1. **Click "New Bucket"**
2. **Bucket Settings:**
   - Name: `ulnec-downloads`
   - Public bucket: **NO** (keep private)
   - File size limit: 100 MB
   - Allowed MIME types: `application/x-msi-installer` or leave empty

3. **Click "Create Bucket"**

---

## 📋 STEP 3: Upload .MSI File (5 minutes)

1. **Click on `ulnec-downloads` bucket**

2. **Click "Upload File"**

3. **Select your .msi file** from your computer

4. **Rename file to:** `UL-NEC-Compliance-Plugin-Latest.msi`
   - Use this exact name for consistency
   - Plugin will look for this filename

5. **Click "Upload"**

6. **Wait for upload to complete** (progress bar)

---

## 📋 STEP 4: Set File Permissions (3 minutes)

### Option A: RLS Policy (Recommended - Secure)

1. **Go to:** Storage → Policies (tab)

2. **Click "New Policy"**

3. **Policy Settings:**
   ```
   Name: Allow authenticated downloads
   Table: objects
   Operation: SELECT
   Target: All
   
   USING expression:
   auth.role() = 'authenticated' AND bucket_id = 'ulnec-downloads'
   ```

4. **Save Policy**

### Option B: Temporary Public Access (For Testing Only)

1. Click on the file
2. Click "Get URL" 
3. Set expiration: 24 hours
4. Copy URL for testing

**⚠️ Remember to set proper RLS after testing!**

---

## 📋 STEP 5: Verify Upload (2 minutes)

1. **Check file appears in bucket:**
   - Should see: `UL-NEC-Compliance-Plugin-Latest.msi`
   - File size should match your original file

2. **Test download (if using public URL):**
   - Copy the public URL
   - Paste in browser
   - File should download

3. **Verify file integrity:**
   - Check downloaded file size
   - Try installing it (on test machine)

---

## 🔧 TROUBLESHOOTING

**Upload fails?**
- Check file size (must be < 100 MB)
- Check internet connection
- Try again in incognito mode
- Clear browser cache

**Can't download file?**
- Verify RLS policy is set
- Check bucket permissions
- Ensure user is authenticated
- Check file path in code

**File corrupted after download?**
- Re-upload file
- Check MIME type settings
- Verify file integrity before upload

---

## ✅ VERIFICATION CHECKLIST

After upload:

- [ ] File visible in Supabase Storage
- [ ] File size matches original
- [ ] Filename is exactly: `UL-NEC-Compliance-Plugin-Latest.msi`
- [ ] RLS policy allows authenticated users to download
- [ ] Test download works (as logged-in user)
- [ ] Downloaded file installs correctly

---

## 📝 File Naming Convention

**Use this structure for version management:**

```
ulnec-downloads/
├── UL-NEC-Compliance-Plugin-Latest.msi    (always points to newest)
├── UL-NEC-Compliance-Plugin-v1.0.0.msi    (specific versions)
├── UL-NEC-Compliance-Plugin-v1.1.0.msi
└── README.txt                              (optional version notes)
```

**For now, just upload:**
- `UL-NEC-Compliance-Plugin-Latest.msi`

---

## 🔒 Security Best Practices

1. ✅ **Never make bucket public** - Use RLS policies
2. ✅ **Verify user license** before allowing download
3. ✅ **Log all downloads** for analytics
4. ✅ **Version your files** for rollback capability
5. ✅ **Scan for malware** before uploading

---

## 📊 Storage Limits

**Supabase PRO Plan:**
- Storage: 100 GB included
- Bandwidth: 250 GB/month
- File size limit: 50 MB default (increase to 100 MB)

**Your .msi file (~10-20 MB):**
- Can serve ~12,500 downloads per month
- Plenty for Beta launch!

---

## 🔗 Integration with Plugin

After upload, the plugin download code already handles it:

**File:** `class-ulnec-download.php`
```php
$file_path = 'ulnec-downloads/UL-NEC-Compliance-Plugin-Latest.msi';
```

**No code changes needed if you use the exact filename!**

---

## 🎯 After Upload

Once file is uploaded:

1. ✅ Test download from WordPress dashboard
2. ✅ Verify license check works before download
3. ✅ Test on multiple browsers
4. ✅ Move to email setup (next phase)

---

## ⏱️ Quick Reference

| Step | Time |
|------|------|
| Access Supabase | 2 min |
| Create bucket | 3 min |
| Upload file | 5 min |
| Set permissions | 3 min |
| Verify | 2 min |
| **Total** | **15 min** |

---

## 🚀 Ready to Upload?

**Quick Steps:**
1. Login to Supabase
2. Storage → ulnec-downloads bucket
3. Upload → Select .msi file
4. Rename to: `UL-NEC-Compliance-Plugin-Latest.msi`
5. Set RLS policy
6. Test download!

---

**After this:** You'll be ready for email setup and final testing!

**Status:** Beta Launch 97% Complete! 🎉
