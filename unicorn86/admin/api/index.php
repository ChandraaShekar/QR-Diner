<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
</head>
<body>
    <script>
    $.post(
        "users/user.php",
        {
            uid: "riu20prfiwhjfoq8",
            name: "Chandra Shekar",
            phone: "+919246249339",
            email: "csr@lbits.co",
            access_right: "2",
            // created_by: "riu20prfiwhjfoq8",
            // restaurant_id: "494834nu"
        },
        function(data, status){
            document.write("Data:"+ data + ",\n Status: " + status);
        }
    )
    </script>  
</body>
</html>