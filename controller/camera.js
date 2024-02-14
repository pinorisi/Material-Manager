const express = require('express');
const multer = require('multer');

const app = express();

// Configure multer to save images to the ../assets/img/photos directory
const storage = multer.diskStorage({
  destination: '../assets/img/uploads',
  filename: function (req, file, cb) {
    cb(null, file.originalname);
  }
});

const upload = multer({ storage: storage });

app.post('/save-image', upload.single('image'), (req, res) => {
  // Image has been uploaded successfully
  res.sendStatus(200);
});

app.listen(3000, () => {
  console.log('Server listening on port 3000');
});

