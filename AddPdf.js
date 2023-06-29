import React, { useState } from 'react';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import Button from 'components/CustomButtons/Button.js';
import CloseIcon from '@mui/icons-material/Close';
import IconButton from '@material-ui/core/IconButton';
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';
import { FormControl, FormLabel } from "@material-ui/core";
import { Http } from "../../utills/Service";
import { apis } from "../../utills/WebConstants";

export default function AddPdf({
  open,
  handleClose,
  fullWidth,
  maxWidth,
  getPdf_Manager,
}) {

  const [title, setTitle] = useState('');
  const [file, setFile] = useState(null);
  const [description, setDescription] = useState('');
  const [selectedFile, setSelectedFile] = useState(null);
  const [errors, setErrors] = useState({});

  const handleFileSelect = (event) => {
    const file = event.target.files[0];
    setSelectedFile(file);
    setFile(selectedFile);
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    // const validationErrors = {};

    // if (!file) {
    //     validationErrors.file = <i>Please select a file !</i>;
    // }

    // if (!title.trim()) {
    //     validationErrors.title = <i>Please enter a title !</i>;
    // }

    // if (Object.keys(validationErrors).length > 0) {
    //     setErrors(validationErrors);
    //     return;
    // }

    // // Clear errors if any
    // setErrors({});


    if (title && selectedFile) {
      const reader = new FileReader();
      reader.onload = () => {
        const pdfUrl = reader.result;
        handleAddPdf({ title, description, pdfUrl });
        setTitle('');
        setDescription('');
        setSelectedFile(null);
        handleClose();
      };
      reader.readAsDataURL(selectedFile);
    }
  };

  // const isFormValid = file && title.trim(); 

  const handleAddPdf = (e) => {
    // e.preventDefault();

    const formData = new FormData();

    formData.append('pdf_title', title);
    formData.append('pdf_desc', description);
    formData.append('pdf_file', selectedFile);


    Http.PostAPI(apis.add_Pdf, formData)
      .then((res) => {
        console.log("Api is Working...", res);
        if (res && res.data && res.data.status) {

          getPdf_Manager();
          handleClose();

        } else {
          console.log('error', res.data);
        }
      })
      .catch((err) => console.log('error', err));
  };

  return (
    <Dialog fullWidth={fullWidth} maxWidth={maxWidth} open={open} onClose={handleClose}>
      <DialogTitle>Add PDF
        <IconButton aria-label="close" onClick={handleClose}
          style={{ color: "black", right: "10px", top: "10px", position: "absolute" }}>
          <CloseIcon />
        </IconButton>
      </DialogTitle>
      <DialogContent style={{ overflowX: 'hidden' }}>
        <FormControl>
          <form onSubmit={handleSubmit}>
            <Box
              component="div"
              sx={{
                '& .MuiTextField-root': { mb: 2 },
              }}
              noValidate
              autoComplete="off"
            >
              <div>
                <FormLabel htmlFor="pdf-title">Title</FormLabel>
                <TextField
                  required
                  id="pdf-title"
                  value={title}
                  onChange={(event) => setTitle(event.target.value)}
                  placeholder="Enter PDF title"
                  fullWidth
                />
                 {/* {errors.title && <span className="error">{errors.title}</span>} */}
              </div>
              <div>
                <FormLabel htmlFor="pdf-description">Description</FormLabel>
                <TextField
               
                  id="pdf-description"
                  placeholder="Enter PDF description"
                  value={description}
                  onChange={(event) => setDescription(event.target.value)}
                  fullWidth
                />
              </div>
              <div>
                <FormLabel htmlFor="pdf-file">PDF File</FormLabel>
                <TextField
                 required
                  id="pdf-file"
                  type="file"
                  accept=".pdf"
                  InputLabelProps={{
                    shrink: true,
                  }}
                  fullWidth
                  onChange={handleFileSelect}
                />
                 {/* {errors.file && <span className="error">{errors.file}</span>} */}
              </div>
            </Box>
            <DialogActions>
              <Button type="submit"  color="primary" fullWidth>
                Add
              </Button>
             
            </DialogActions>
            {/* {!isFormValid } */}
          </form>
        </FormControl>
      </DialogContent>
    </Dialog>
  );
}