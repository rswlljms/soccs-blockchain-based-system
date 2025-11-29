# Re-Registration Flow for Rejected Students

## ✅ **New Feature: Re-Registration Support**

Rejected students can now register again with correct documents!

## 🔄 **Registration Flow:**

### **1st Registration (Wrong Documents):**
1. Student submits form with wrong documents
2. System runs instant verification
3. **Result:** ❌ **Rejected** - "Name not found in documents"
4. **Status:** `rejected` in database
5. **Email:** Rejection notification sent

### **2nd Registration (Correct Documents):**
1. **Same student ID/email** tries to register again
2. System detects: "This is a re-registration"
3. **Updates** existing record instead of creating new one
4. Runs verification on new documents
5. **Result:** ✅ **Approved** - "Re-registration approved!"
6. **Status:** `approved` in database
7. **Email:** Approval with password setup link

## 🚫 **Blocked Scenarios:**

| Status | Can Re-register? | Message |
|--------|------------------|---------|
| `pending` | ❌ **NO** | "Registration is already pending. Please wait for approval." |
| `approved` | ❌ **NO** | "Registration already approved. Please check your email for login details." |
| `rejected` | ✅ **YES** | Updates existing record |

## 📧 **Email Messages:**

### **Approved Re-registration:**
> "Re-registration approved! Check your email to set your password."

### **Rejected Re-registration:**
> "Re-registration rejected: [reason] You can try registering again with correct documents."

## 🗄️ **Database Changes:**

- **Rejected records** are **updated** (not duplicated)
- **Old rejection data** is cleared (`rejected_at=NULL`, `rejection_reason=NULL`)
- **New documents** replace old ones
- **Status** resets to `pending` then `approved`/`rejected`

## 🧪 **Test Steps:**

1. **Register with wrong documents** → Should reject
2. **Try same student ID again** → Should allow re-registration
3. **Submit correct documents** → Should approve
4. **Try again after approval** → Should block (already approved)
