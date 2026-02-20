<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "../../config/koneksi.php";

// Terima input JSON
$input = json_decode(file_get_contents("php://input"), true);

$userid = $input["userid"] ?? null;
$password = $input["password"] ?? null;

// Validasi input
if (empty($userid) || empty($password)) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "User ID dan Password harus diisi"
    ]);
    exit;
}

// Enkripsi password dengan HMAC SHA256
$secret_key = 'marinuak';
$userpwd = hash_hmac("sha256", $password, $secret_key);

try {
    // LANGKAH 1: Cek user di tabel ad_muser
    $sqlCheck = "
        SELECT 
            a.ad_muser_key,
            a.userid,
            a.username,
            a.status,
            a.ad_mclient_key,
            a.ad_morg_key,
            a.ad_mrole_key,
            a.email,
            a.phone,
            a.avatar,
            a.theme,
            a.direction,
            a.themetype,
            a.gradient,
            a.decoration,
            a.bgposition,
            a.layout,
            b.name as role_name,
            c.name as org_name,
            d.name as client_name,
            e.code as lang
        FROM ad_muser a
        LEFT JOIN ad_mrole b ON a.ad_mrole_key = b.ad_mrole_key
        LEFT JOIN ad_morg c ON a.ad_morg_key = c.ad_morg_key
        LEFT JOIN ad_mclient d ON a.ad_mclient_key = d.ad_mclient_key
        LEFT JOIN ad_slanguage e ON a.ad_slanguage_key = e.ad_slanguage_key
        WHERE a.userid = :userid AND a.userpwd = :userpwd
    ";
    
    $stmtCheck = $connec->prepare($sqlCheck);
    $stmtCheck->bindParam(":userid", $userid);
    $stmtCheck->bindParam(":userpwd", $userpwd);
    $stmtCheck->execute();
    $userData = $stmtCheck->fetch(PDO::FETCH_ASSOC);
    
    // Jika user tidak ditemukan
    if (!$userData) {
        // Cek apakah userid ada
        $sqlExist = "SELECT status FROM ad_muser WHERE userid = :userid";
        $stmtExist = $connec->prepare($sqlExist);
        $stmtExist->bindParam(":userid", $userid);
        $stmtExist->execute();
        $userExist = $stmtExist->fetch(PDO::FETCH_ASSOC);
        
        if ($userExist) {
            if ($userExist['status'] == '0') {
                $message = "Akun terkunci";
            } else {
                $message = "Password salah";
            }
        } else {
            $message = "User tidak terdaftar";
        }
        
        echo json_encode([
            "status" => "ERROR",
            "message" => $message
        ]);
        exit;
    }
    
    // LANGKAH 2: Cek cashier balance
    $user_key = $userData['ad_muser_key'];
    
    $sqlCashier = "
        SELECT 
            pos_dcashierbalance_key,
            pos_mcashier_key,
            pos_mshift_key,
            startdate,
            enddate,
            balanceamount,
            salesamount,
            status
        FROM pos_dcashierbalance 
        WHERE ad_muser_key = :user_key 
            AND status = 'RUNNING'
            AND DATE(startdate) = CURRENT_DATE
        ORDER BY startdate DESC
        LIMIT 1
    ";
    
    $stmtCashier = $connec->prepare($sqlCashier);
    $stmtCashier->bindParam(":user_key", $user_key);
    $stmtCashier->execute();
    $cashierBalance = $stmtCashier->fetch(PDO::FETCH_ASSOC);
    
    // Jika tidak ada cashier balance
    if (!$cashierBalance) {
        echo json_encode([
            "status" => "ERROR",
            "message" => "Tidak ada sesi kasir yang aktif untuk hari ini",
            "user_data" => [
                "id" => $user_key,
                "userid" => $userData['userid'],
                "username" => $userData['username']
            ]
        ]);
        exit;
    }
    
    // LANGKAH 3: Ambil menu, role, org dari function (opsional)
    // Kita coba panggil function, tapi jika error kita tetap return data user
    $menu = [];
    $role = [];
    $org = [];
    $lastpage = [];
    
    try {
        $sqlFunc = "SELECT * FROM proc_ad_muser_login_get(
            :p_userid,
            :p_userpwd
        )";
        
        $stmtFunc = $connec->prepare($sqlFunc);
        $stmtFunc->bindParam(":p_userid", $userid);
        $stmtFunc->bindParam(":p_userpwd", $userpwd);
        $stmtFunc->execute();
        $resultFunc = $stmtFunc->fetch(PDO::FETCH_ASSOC);
        
        if ($resultFunc) {
            $menu = $resultFunc["o_menu"] ? json_decode($resultFunc["o_menu"], true) : [];
            $role = $resultFunc["o_role"] ? json_decode($resultFunc["o_role"], true) : [];
            $org = $resultFunc["o_org"] ? json_decode($resultFunc["o_org"], true) : [];
            $lastpage = $resultFunc["o_lastpage"] ? json_decode($resultFunc["o_lastpage"], true) : [];
        }
    } catch (Exception $e) {
        // Abaikan error, tetap lanjut
        error_log("Error calling proc_ad_muser_login_get: " . $e->getMessage());
    }
    
    // Format data user sesuai struktur yang diharapkan
    $formattedUser = [
        "id" => $userData['ad_muser_key'],
        "cln" => $userData['ad_mclient_key'],
        "org" => $userData['ad_morg_key'],
        "role" => $userData['ad_mrole_key'],
        "cln_name" => $userData['client_name'],
        "org_name" => $userData['org_name'],
        "org_cover" => null,
        "org_avatar" => null,
        "role_name" => $userData['role_name'],
        "userid" => $userData['userid'],
        "username" => $userData['username'],
        "avatar" => $userData['avatar'],
        "status" => "online",
        "lang" => $userData['lang'] ?? 'en',
        "email" => $userData['email'],
        "phone" => $userData['phone'],
        "description" => null,
        "theme" => $userData['theme'] ?? 'greenPurpleTheme',
        "direction" => $userData['direction'] ?? 'ltr',
        "type" => $userData['themetype'] ?? 'dark',
        "gradient" => $userData['gradient'] ?? true,
        "decoration" => $userData['decoration'] ?? true,
        "bgposition" => $userData['bgposition'] ?? 'half',
        "layout" => $userData['layout'] ?? 'left-sidebar',
        "cashier_balance" => $cashierBalance
    ];
    
    // Login sukses
    echo json_encode([
        "status" => "SUCCESS",
        "message" => "Login berhasil",
        "user_data" => $formattedUser,
        "menu" => $menu,
        "role" => $role,
        "org" => $org,
        "lastpage" => $lastpage
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        "status" => "ERROR",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>