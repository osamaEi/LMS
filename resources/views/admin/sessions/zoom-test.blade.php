<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Zoom Join</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        button { padding: 10px 20px; font-size: 16px; cursor: pointer; }
        #output { margin-top: 20px; padding: 10px; background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Test Zoom Button</h1>
    <button id="test-btn" onclick="testClick()">Click Me!</button>
    <div id="output"></div>

    <script>
        function testClick() {
            document.getElementById('output').innerHTML = 'Button clicked at ' + new Date().toLocaleTimeString();
            console.log('Button clicked!');
            alert('Button works!');
        }

        console.log('Script loaded');
    </script>
</body>
</html>
