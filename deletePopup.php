 <!-- Modal for deletion confirmation -->
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
 </head>
 <body>
 <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this user?</p>
            <button class="confirm-btn" onclick="confirmDelete()">Yes, Delete</button>
            <button class="cancel-btn" onclick="hideModal()">Cancel</button>
        </div>
    </div>
    <script src="script.js"></script>
 </body>
 </html>