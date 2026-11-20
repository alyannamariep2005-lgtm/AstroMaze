
<?php

// Check authentication and user role
require_once '../includes/auth.php';
require_role('user');

// Connect to the database
require_once '../includes/db.php';

// Get the currently logged-in user's ID
$userId = $_SESSION['user_id'];

// Retrieve the user's information
$query = mysqli_prepare(
    $conn,
    "SELECT username, email, password, profile_picture
     FROM users WHERE user_id = ?"
);

mysqli_stmt_bind_param($query, "i", $userId);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($query);

// Check if the account exists
if (!$user) {
    exit("User account not found.");
}

// Initialize feedback messages
$success = "";
$error = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get submitted account information
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Get password form values
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Keep existing password and profile picture by default
    $passwordToSave = $user['password'];
    $pictureToSave = $user['profile_picture'];

    $newUploadedPath = null;

    // Validate empty fields
    if (empty($username) || empty($email)) {

        $error = "Please fill in all account information.";

    // Validate email format
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    // Check whether the username or email is already taken
    if (empty($error)) {

        $checkQuery = mysqli_prepare(
            $conn,
            "SELECT user_id FROM users
             WHERE (username = ? OR email = ?)
             AND user_id != ?"
        );

        mysqli_stmt_bind_param(
            $checkQuery,
            "ssi",
            $username,
            $email,
            $userId
        );

        mysqli_stmt_execute($checkQuery);

        $checkResult = mysqli_stmt_get_result($checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $error = "Username or email is already taken.";
        }

        mysqli_stmt_close($checkQuery);
    }

    // Validate password change if requested
    if (
        empty($error) &&
        (
            $currentPassword !== '' ||
            $newPassword !== '' ||
            $confirmPassword !== ''
        )
    ) {

        if (
            empty($currentPassword) ||
            empty($newPassword) ||
            empty($confirmPassword)
        ) {

            $error = "Please complete all password fields.";

        } elseif (!password_verify($currentPassword, $user['password'])) {

            $error = "Your current password is incorrect.";

        } elseif ($newPassword !== $confirmPassword) {

            $error = "New passwords do not match.";

        } elseif (strlen($newPassword) < 8) {

            $error = "Your new password must be at least 8 characters.";

        } else {

            $passwordToSave = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

        }
    }

    // Handle profile picture upload
    if (
        empty($error) &&
        isset($_FILES['profile_picture']) &&
        $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $file = $_FILES['profile_picture'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {

            $error = "There was a problem uploading your picture.";

        // Check file size (maximum 2 MB)
        } elseif ($file['size'] > 2 * 1024 * 1024) {

            $error = "Profile picture must not exceed 2 MB.";

        } else {

            // Check actual image type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!array_key_exists($mimeType, $allowedTypes)) {

                $error = "Only JPG, PNG, and WEBP images are allowed.";

            } elseif (getimagesize($file['tmp_name']) === false) {

                $error = "The uploaded file is not a valid image.";

            } else {

                // Create a unique filename
                $extension = $allowedTypes[$mimeType];

                $fileName = 'user_' . $userId . '_' .
                    bin2hex(random_bytes(8)) . '.' . $extension;

                // Set upload directory
                $uploadDirectory = __DIR__ .
                    '/../uploads/profile_pictures/';

                // Check whether the folder is available
                if (
                    !is_dir($uploadDirectory) ||
                    !is_writable($uploadDirectory)
                ) {

                    $error = "The profile picture folder is unavailable.";

                } else {

                    $destination = $uploadDirectory . $fileName;

                    // Move uploaded image to the folder
                    if (move_uploaded_file($file['tmp_name'], $destination)) {

                        $pictureToSave =
                            'uploads/profile_pictures/' . $fileName;

                        $newUploadedPath = $destination;

                    } else {

                        $error = "Unable to save the uploaded picture.";

                    }
                }
            }
        }
    }

    // Update account information if all validations pass
    if (empty($error)) {

        $updateQuery = mysqli_prepare(
            $conn,
            "UPDATE users
             SET username = ?, email = ?, password = ?, profile_picture = ?
             WHERE user_id = ?"
        );

        mysqli_stmt_bind_param(
            $updateQuery,
            "ssssi",
            $username,
            $email,
            $passwordToSave,
            $pictureToSave,
            $userId
        );

        if (mysqli_stmt_execute($updateQuery)) {

            // Update session username
            $_SESSION['username'] = $username;

            // Update displayed account information
            $oldPicture = $user['profile_picture'];

            $user['username'] = $username;
            $user['email'] = $email;
            $user['password'] = $passwordToSave;
            $user['profile_picture'] = $pictureToSave;

            // Delete old uploaded picture after successful replacement
            if (
                $newUploadedPath !== null &&
                !empty($oldPicture) &&
                strpos($oldPicture, 'uploads/profile_pictures/') === 0
            ) {

                $oldFile = __DIR__ . '/../' . $oldPicture;

                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            $success = "Profile updated successfully!";

        } else {

            // Remove newly uploaded image if database update fails
            if ($newUploadedPath !== null && is_file($newUploadedPath)) {
                unlink($newUploadedPath);
            }

            $error = "Something went wrong. Please try again.";

        }

        mysqli_stmt_close($updateQuery);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Profile | AstroMaze</title>

    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/edit-profile.css">
</head>

<body>

    <?php include '../includes/navbar.php'; ?>

    <main class="edit-profile-container">

        <section class="edit-profile-header">
            <h1>Edit Profile</h1>
            <p>Manage your account information</p>
        </section>

        <section class="edit-profile-card">

            <?php if (!empty($success)): ?>
                <p class="form-success">
                    <?php echo htmlspecialchars($success); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <p class="form-error">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">

                <!-- PROFILE PICTURE -->
                <div class="profile-section">

                    <h2>Profile Picture</h2>

                    <div class="profile-picture-area">

                        <div class="profile-picture-preview">

                            <?php if (!empty($user['profile_picture'])): ?>

                                <img
                                    src="../<?php echo htmlspecialchars($user['profile_picture']); ?>"
                                    id="picturePreview"
                                    alt="Profile Picture"
                                >

                            <?php else: ?>

                                <img
                                    src=""
                                    id="picturePreview"
                                    alt="Profile Picture Preview"
                                    style="display: none;"
                                >

                                <span id="picturePlaceholder">
                                    No Profile Picture
                                </span>

                            <?php endif; ?>

                        </div>

                        <div class="form-group">

                            <label for="profile_picture">
                                Upload New Picture
                            </label>

                            <input
                                type="file"
                                id="profile_picture"
                                name="profile_picture"
                                accept="image/jpeg,image/png,image/webp"
                            >

                            <small>
                                JPG, PNG, or WEBP. Maximum size: 2 MB.
                            </small>

                        </div>

                    </div>

                </div>

                <!-- ACCOUNT INFORMATION -->
                <div class="profile-section">

                    <h2>Account Information</h2>

                    <div class="form-group">
                        <label for="username">Username</label>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo htmlspecialchars($user['username']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            required
                        >
                    </div>

                </div>

                <!-- CHANGE PASSWORD -->
                <div class="profile-section">

                    <h2>Change Password</h2>

                    <div class="form-group">
                        <label for="current_password">Current Password</label>

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            placeholder="Enter current password"
                        >
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            placeholder="Enter new password"
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Confirm new password"
                        >
                    </div>

                </div>

                <!-- FORM ACTIONS -->
                <div class="profile-actions">

                    <button type="submit" name="save_profile">
                        Save Changes
                    </button>

                    <a href="home.php">Cancel</a>

                </div>

            </form>

        </section>

    </main>

    <!-- IMAGE PREVIEW SCRIPT -->
    <script>

        const pictureInput = document.getElementById('profile_picture');
        const picturePreview = document.getElementById('picturePreview');
        const picturePlaceholder = document.getElementById('picturePlaceholder');

        pictureInput.addEventListener('change', function () {

            const file = this.files[0];

            if (file) {

                const imageURL = URL.createObjectURL(file);

                picturePreview.src = imageURL;
                picturePreview.style.display = 'block';

                if (picturePlaceholder) {
                    picturePlaceholder.style.display = 'none';
                }

            }

        });

    </script>

</body>
</html>
