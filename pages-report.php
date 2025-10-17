<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Report Issue - Eduwide</title>
    <link rel="icon" href="assets/images/logos/favicon.png">
    <?php include_once ("includes/css-links-inc.php"); ?>

    <style>
        .alert-success {
            background-color: green;
            color: white;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .alert-danger {
            background-color: red;
            color: white;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .photo-upload-area {
            border: 2px dashed #ccc;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .photo-preview img {
            max-width: 80px;
            border-radius: 6px;
            margin: 5px;
        }

        .priority-options {
            display: flex;
            gap: 10px;
        }

        .priority-option {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
        }

        .priority-option.selected {
            border-color: #007bff;
            background-color: #e7f1ff;
        }
    </style>

</head>

<body>

<main>
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <img src="assets/images/logos/eduwide-logo.png" alt="" style="max-height:150px;"><br>
            <div class="col-lg-6 col-md-8 card p-4">
                <div class="pt-4 pb-2">
                    
                    <h5 class="card-title text-center pb-0 fs-4">Report Your Issue</h5>
                </div>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger" id="alert-message">
                        <?php echo $_SESSION['error_message']; ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php elseif (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success" id="alert-message">
                        <?php echo $_SESSION['success_message']; ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <form id="reportForm" method="POST" enctype="multipart/form-data" action="report-backend.php">

                    <h6>Report Details</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="reporterName" class="form-label">Your Name</label>
                            <input type="text" class="form-control" name="reporterName" id="reporterName" placeholder="Enter your full name">
                        </div>
                        <div class="col-md-6">
                            <label for="reportDate" class="form-label">Date of Incident</label>
                            <input type="date" class="form-control" name="reportDate" id="reportDate">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reportTitle" class="form-label">Report Title</label>
                        <input type="text" class="form-control" name="reportTitle" id="reportTitle" placeholder="Brief description of the issue">
                    </div>

                    <div class="mb-3">
                        <label for="issueType" class="form-label">Issue Type</label>
                        <select class="form-select" name="issueType" id="issueType">
                            <option selected>Select issue type</option>
                            <option value="bug">Bug</option>
                            <option value="technical">Technical Issue</option>
                            <option value="safety">Safety Concern</option>
                            <option value="maintenance">Maintenance Request</option>
                            <option value="complaint">Complaint</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="issueDescription" class="form-label">Issue Description</label>
                        <textarea class="form-control" name="issueDescription" id="issueDescription" rows="5" placeholder="Provide detailed information about the issue..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" id="location" placeholder="Where did this occur?">
                    </div>

                    <div class="mb-3">
                        <label for="referenceLink" class="form-label">Reference Link (Optional)</label>
                        <input type="url" class="form-control" name="referenceLink" id="referenceLink" placeholder="https://example.com">
                    </div>

                    <h6>Priority Level</h6>
                    <div class="priority-options">
                        <div class="priority-option selected" data-priority="low">Low</div>
                        <div class="priority-option" data-priority="medium">Medium</div>
                        <div class="priority-option" data-priority="high">High</div>
                    </div>
                    <input type="hidden" name="priorityLevel" id="priorityLevel" value="low">

                    <h6 class="mt-3">Attach Photos</h6>
                    <div class="photo-upload-area" id="photoUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drag & drop photos here or click to browse</p>
                        <button type="button" class="btn btn-outline" id="selectPhotosBtn">Select Photos</button>
                        <input type="file" name="photos[]" id="photoInput" multiple accept="image/*" style="display: none;">
                    </div>
                    <div class="photo-preview" id="photoPreview"></div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane"></i> Submit Report
                        </button>
                    </div>
                </form>

            </div>
        </section>
    </div>
</main>

<?php include_once ("includes/js-links-inc.php") ?>

<script>
    // Priority selection
    const priorityOptions = document.querySelectorAll('.priority-option');
    const priorityLevelInput = document.getElementById('priorityLevel');

    priorityOptions.forEach(option => {
        option.addEventListener('click', () => {
            priorityOptions.forEach(opt => opt.classList.remove('selected'));
            option.classList.add('selected');
            priorityLevelInput.value = option.dataset.priority;
        });
    });

    // Photo upload and preview
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const selectBtn = document.getElementById('selectPhotosBtn');
    const uploadArea = document.getElementById('photoUploadArea');

    selectBtn.addEventListener('click', () => photoInput.click());
    uploadArea.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', () => {
        Array.from(photoInput.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const imgContainer = document.createElement('div');
                imgContainer.style.display = 'inline-block';
                imgContainer.style.position = 'relative';
                imgContainer.style.margin = '5px';

                const img = document.createElement('img');
                img.src = e.target.result;
                imgContainer.appendChild(img);

                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = '&times;';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '0';
                removeBtn.style.right = '0';
                removeBtn.style.background = 'red';
                removeBtn.style.color = 'white';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.padding = '2px 6px';
                removeBtn.addEventListener('click', () => {
                    const dt = new DataTransfer();
                    Array.from(photoInput.files)
                        .filter((_, i) => i !== index)
                        .forEach(f => dt.items.add(f));
                    photoInput.files = dt.files;
                    imgContainer.remove();
                });
                imgContainer.appendChild(removeBtn);

                photoPreview.appendChild(imgContainer);
            };
            reader.readAsDataURL(file);
        });
    });

    // Auto hide alerts
    setTimeout(() => {
        const alertMessage = document.getElementById('alert-message');
        if(alertMessage) alertMessage.style.display = 'none';
    }, 10000);
</script>

</body>
</html>
