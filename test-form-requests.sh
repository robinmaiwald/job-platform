#!/bin/bash

BASE_URL="http://127.0.0.1:8000/api"

PASS=0
FAIL=0

print_result() {
    local name="$1"
    local expected="$2"
    local actual="$3"

    if [ "$actual" = "$expected" ]; then
        echo "✅ PASS: $name ($actual)"
        ((PASS++))
    else
        echo "❌ FAIL: $name (expected $expected, got $actual)"
        ((FAIL++))
    fi
}

request() {
    curl -s -o /tmp/test-response.json -w "%{http_code}" "$@"
}

echo ""
echo "========================================"
echo " Laravel Form Request Smoke Tests"
echo "========================================"
echo ""

# --------------------------------------------------
# USER LOGIN
# --------------------------------------------------

echo "---- USER TESTS ----"

USER_EMAIL="${USER_EMAIL:-test@example.com}"
USER_PASSWORD="${USER_PASSWORD:-password}"

STATUS=$(request \
    -X POST "$BASE_URL/login" \
    -H "Content-Type: application/json" \
    -d "{\"email\":\"$USER_EMAIL\",\"password\":\"$USER_PASSWORD\"}")

print_result "User login" "200" "$STATUS"

USER_TOKEN=$(cat /tmp/test-response.json | php -r '
$json = json_decode(stream_get_contents(STDIN), true);
echo $json["token"] ?? "";
')

if [ -z "$USER_TOKEN" ]; then
    echo "❌ Could not obtain user token."
    exit 1
fi

# --------------------------------------------------
# USER STORE VALIDATION
# --------------------------------------------------

STATUS=$(request \
    -X POST "$BASE_URL/users" \
    -H "Authorization: Bearer $USER_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"name":"Smoke Test User","email":"invalid","password":"short"}')

print_result "User validation rejects invalid data" "422" "$STATUS"

# --------------------------------------------------
# COMPANY STORE VALIDATION
# --------------------------------------------------

echo ""
echo "---- COMPANY TESTS ----"

STATUS=$(request \
    -X POST "$BASE_URL/companies" \
    -H "Authorization: Bearer $USER_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"name":"","website":"not-a-url"}')

print_result "Company validation rejects invalid data" "422" "$STATUS"

# Create valid company
STATUS=$(request \
    -X POST "$BASE_URL/companies" \
    -H "Authorization: Bearer $USER_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"name":"Smoke Test Company","description":"Form request test","website":"https://example.com"}')

print_result "Company creation" "201" "$STATUS"

COMPANY_ID=$(cat /tmp/test-response.json | php -r '
$json = json_decode(stream_get_contents(STDIN), true);
echo $json["id"] ?? "";
')

if [ -z "$COMPANY_ID" ]; then
    echo "❌ Could not obtain company ID."
    exit 1
fi

# --------------------------------------------------
# COMPANY UPDATE VALIDATION
# --------------------------------------------------

STATUS=$(request \
    -X PUT "$BASE_URL/companies/$COMPANY_ID" \
    -H "Authorization: Bearer $USER_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"name":"Updated Company","website":"invalid-url"}')

print_result "Company update validation" "422" "$STATUS"

# --------------------------------------------------
# JOB STORE VALIDATION
# --------------------------------------------------

echo ""
echo "---- JOB TESTS ----"

STATUS=$(request \
    -X POST "$BASE_URL/jobs" \
    -H "Authorization: Bearer $USER_TOKEN" \
    -H "Content-Type: application/json" \
    -d "{\"company_id\":999999,\"title\":\"Test Job\"}")

print_result "Job validation rejects invalid company" "422" "$STATUS"

STATUS=$(request \
    -X POST "$BASE_URL/jobs" \
    -H "Authorization: Bearer $USER_TOKEN" \
    -H "Content-Type: application/json" \
    -d "{\"company_id\":$COMPANY_ID}")

print_result "Job validation rejects missing title" "422" "$STATUS"

# --------------------------------------------------
# ADMIN LOGIN
# --------------------------------------------------

echo ""
echo "---- ADMIN TESTS ----"

ADMIN_NAME="${ADMIN_NAME:-admin}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-password}"

STATUS=$(request \
    -X POST "$BASE_URL/admin/login" \
    -H "Content-Type: application/json" \
    -d "{\"name\":\"$ADMIN_NAME\",\"password\":\"$ADMIN_PASSWORD\"}")

print_result "Admin login" "200" "$STATUS"

ADMIN_TOKEN=$(cat /tmp/test-response.json | php -r '
$json = json_decode(stream_get_contents(STDIN), true);
echo $json["token"] ?? "";
')

if [ -z "$ADMIN_TOKEN" ]; then
    echo "❌ Could not obtain admin token."
    exit 1
fi

# --------------------------------------------------
# ADMIN LOGIN VALIDATION
# --------------------------------------------------

STATUS=$(request \
    -X POST "$BASE_URL/admin/login" \
    -H "Content-Type: application/json" \
    -d '{"name":"","password":""}')

print_result "Admin login validation" "422" "$STATUS"

# --------------------------------------------------
# ADMIN USER VALIDATION
# --------------------------------------------------

STATUS=$(request \
    -X POST "$BASE_URL/admin/users" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"name":"","email":"bad-email","password":"short"}')

print_result "Admin user creation validation" "422" "$STATUS"

# --------------------------------------------------
# ADMIN JOB VALIDATION
# --------------------------------------------------

STATUS=$(request \
    -X POST "$BASE_URL/admin/jobs" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"company_id":999999,"title":""}')

print_result "Admin job creation validation" "422" "$STATUS"

# --------------------------------------------------
# ADMIN COMPANY VALIDATION
# --------------------------------------------------

STATUS=$(request \
    -X POST "$BASE_URL/admin/companies" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"name":"","owner_id":999999}')

print_result "Admin company creation validation" "422" "$STATUS"

# --------------------------------------------------
# SUMMARY
# --------------------------------------------------

echo ""
echo "========================================"
echo " Test Summary"
echo "========================================"
echo "Passed: $PASS"
echo "Failed: $FAIL"
echo "========================================"

if [ "$FAIL" -eq 0 ]; then
    echo "🎉 All Form Request smoke tests passed."
    exit 0
else
    echo "⚠️ Some tests failed."
    exit 1
fi