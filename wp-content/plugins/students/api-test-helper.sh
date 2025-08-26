#!/bin/bash

# API Test Helper Script
# This script automates login, nonce retrieval, and API testing

echo "🔐 WordPress API Test Helper"
echo "============================"

# Configuration
WORDPRESS_URL="http://localhost/devrix-test-project"
USERNAME="sasho"
PASSWORD="Jana009@"
COOKIES_FILE="cookies.txt"

echo "📝 Configuration:"
echo "  URL: $WORDPRESS_URL"
echo "  User: $USERNAME"
echo "  Cookies: $COOKIES_FILE"
echo ""

# Step 1: Login
echo "1️⃣ Logging in..."
curl -c $COOKIES_FILE -d "log=$USERNAME&pwd=$PASSWORD&wp-submit=Log+In" "$WORDPRESS_URL/wp-login.php" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Login successful"
else
    echo "❌ Login failed"
    exit 1
fi

# Step 2: Get fresh nonce
echo ""
echo "2️⃣ Getting fresh nonce..."
NONCE=$(curl -b $COOKIES_FILE "$WORDPRESS_URL/wp-content/plugins/students/get-nonce.php" 2>/dev/null | grep -o 'cb25af6567\|ea8716b065\|[a-f0-9]\{10\}' | head -1)

if [ -z "$NONCE" ]; then
    echo "❌ Could not get nonce"
    exit 1
fi

echo "✅ Nonce: $NONCE"

# Step 3: Test API endpoints
echo ""
echo "3️⃣ Testing API endpoints..."

# Test 1: Create student
echo "   📝 Creating student..."
CREATE_RESPONSE=$(curl -s -X POST "$WORDPRESS_URL/?rest_route=/students/v1/students" \
  -b $COOKIES_FILE \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: $NONCE" \
  -d '{
    "title": "Script Test Student",
    "student_id": "SCRIPT_'$(date +%s)'",
    "student_email": "script.test@example.com",
    "student_is_active": "active"
  }')

echo "$CREATE_RESPONSE" | jq '.' 2>/dev/null || echo "$CREATE_RESPONSE"

# Extract student ID if creation was successful
STUDENT_ID=$(echo "$CREATE_RESPONSE" | grep -o '"id":[0-9]*' | cut -d':' -f2)

if [ ! -z "$STUDENT_ID" ]; then
    echo "   ✅ Student created with ID: $STUDENT_ID"
    
    # Test 2: Update student
    echo ""
    echo "   📝 Updating student..."
    UPDATE_RESPONSE=$(curl -s -X PUT "$WORDPRESS_URL/?rest_route=/students/v1/students/$STUDENT_ID" \
      -b $COOKIES_FILE \
      -H "Content-Type: application/json" \
      -H "X-WP-Nonce: $NONCE" \
      -d '{
        "title": "Updated Script Test Student",
        "student_is_active": "inactive"
      }')
    
    echo "$UPDATE_RESPONSE" | jq '.' 2>/dev/null || echo "$UPDATE_RESPONSE"
    
    # Test 3: Delete student
    echo ""
    echo "   📝 Deleting student..."
    DELETE_RESPONSE=$(curl -s -X DELETE "$WORDPRESS_URL/?rest_route=/students/v1/students/$STUDENT_ID" \
      -b $COOKIES_FILE \
      -H "X-WP-Nonce: $NONCE")
    
    echo "$DELETE_RESPONSE" | jq '.' 2>/dev/null || echo "$DELETE_RESPONSE"
    
else
    echo "   ❌ Student creation failed"
fi

# Test 4: Get all students
echo ""
echo "   📝 Getting all students..."
GET_RESPONSE=$(curl -s -X GET "$WORDPRESS_URL/?rest_route=/students/v1/students" \
  -b $COOKIES_FILE \
  -H "X-WP-Nonce: $NONCE")

STUDENT_COUNT=$(echo "$GET_RESPONSE" | grep -o '"id":[0-9]*' | wc -l)
echo "   ✅ Found $STUDENT_COUNT students"

echo ""
echo "🎯 Testing complete!"
echo "📋 Nonce used: $NONCE"
echo "🍪 Cookies saved: $COOKIES_FILE"
echo ""
echo "💡 To test manually, use:"
echo "   curl -X POST \"$WORDPRESS_URL/?rest_route=/students/v1/students\" \\"
echo "     -b $COOKIES_FILE \\"
echo "     -H \"Content-Type: application/json\" \\"
echo "     -H \"X-WP-Nonce: $NONCE\" \\"
echo "     -d '{\"title\":\"Test\",\"student_id\":\"TEST\"}'"
