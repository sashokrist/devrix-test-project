#!/bin/bash

# Console Commands for Students API
# Replace STUDENT_ID with actual student ID (e.g., 173)
# Replace NONCE with fresh nonce from ./api-test-helper.sh

echo "🎓 Students API Console Commands"
echo "================================"
echo ""
echo "📋 Current Nonce: d5f78e3fea"
echo "📋 Test Student ID: 173"
echo ""
echo "🔧 Commands to copy and paste:"
echo ""

echo "1️⃣ UPDATE Student (Name, Email, Status):"
echo 'curl -X PUT "http://localhost/devrix-test-project/?rest_route=/students/v1/students/173" \'
echo '  -b cookies.txt \'
echo '  -H "Content-Type: application/json" \'
echo '  -H "X-WP-Nonce: d5f78e3fea" \'
echo '  -d '"'"'{
    "title": "Updated Console Student",
    "student_email": "updated.console@example.com",
    "student_is_active": "inactive"
  }'"'"''
echo ""

echo "2️⃣ UPDATE Student (Name Only):"
echo 'curl -X PUT "http://localhost/devrix-test-project/?rest_route=/students/v1/students/173" \'
echo '  -b cookies.txt \'
echo '  -H "Content-Type: application/json" \'
echo '  -H "X-WP-Nonce: d5f78e3fea" \'
echo '  -d '"'"'{"title": "New Student Name"}'"'"''
echo ""

echo "3️⃣ UPDATE Student (Email Only):"
echo 'curl -X PUT "http://localhost/devrix-test-project/?rest_route=/students/v1/students/173" \'
echo '  -b cookies.txt \'
echo '  -H "Content-Type: application/json" \'
echo '  -H "X-WP-Nonce: d5f78e3fea" \'
echo '  -d '"'"'{"student_email": "new.email@example.com"}'"'"''
echo ""

echo "4️⃣ DELETE Student (Soft Delete):"
echo 'curl -X DELETE "http://localhost/devrix-test-project/?rest_route=/students/v1/students/173" \'
echo '  -b cookies.txt \'
echo '  -H "X-WP-Nonce: d5f78e3fea"'
echo ""

echo "5️⃣ DELETE Student (Force Delete):"
echo 'curl -X DELETE "http://localhost/devrix-test-project/?rest_route=/students/v1/students/173?force=true" \'
echo '  -b cookies.txt \'
echo '  -H "X-WP-Nonce: d5f78e3fea"'
echo ""

echo "6️⃣ GET All Students:"
echo 'curl -X GET "http://localhost/devrix-test-project/?rest_route=/students/v1/students" \'
echo '  -b cookies.txt \'
echo '  -H "X-WP-Nonce: d5f78e3fea"'
echo ""

echo "7️⃣ GET Specific Student:"
echo 'curl -X GET "http://localhost/devrix-test-project/?rest_route=/students/v1/students/173" \'
echo '  -b cookies.txt \'
echo '  -H "X-WP-Nonce: d5f78e3fea"'
echo ""

echo "8️⃣ GET Active Students Only:"
echo 'curl -X GET "http://localhost/devrix-test-project/?rest_route=/students/v1/students/active" \'
echo '  -b cookies.txt \'
echo '  -H "X-WP-Nonce: d5f78e3fea"'
echo ""

echo "9️⃣ CREATE New Student:"
echo 'curl -X POST "http://localhost/devrix-test-project/?rest_route=/students/v1/students" \'
echo '  -b cookies.txt \'
echo '  -H "Content-Type: application/json" \'
echo '  -H "X-WP-Nonce: d5f78e3fea" \'
echo '  -d '"'"'{
    "title": "Console Test Student",
    "student_id": "CONSOLE_001",
    "student_email": "console.test@example.com",
    "student_is_active": "active"
  }'"'"''
echo ""

echo "⚠️  Important Notes:"
echo "- Replace '173' with actual student ID"
echo "- Replace 'd5f78e3fea' with fresh nonce from ./api-test-helper.sh"
echo "- Run ./api-test-helper.sh to get fresh credentials"
echo ""
echo "💡 Tip: Copy and paste these commands one by one to test!"
