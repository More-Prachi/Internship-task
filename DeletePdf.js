import React from 'react';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import Button from 'components/CustomButtons/Button.js';
import CloseIcon from '@mui/icons-material/Close';
import IconButton from '@material-ui/core/IconButton';
import { Http } from "../../utills/Service";
import{apis} from "../../utills/WebConstants";

export default function DeletePdf({
  open,
  handleClose,
  handleDeletePdf,
  selectedPdfId,
  fullWidth,
  getPdf_Manager,
  maxWidth,
}) {
  const handleConfirmDelete = () => {
    // handleDeletePdf(selectedPdf);
    // handleClose();
    const formData = new FormData();
    formData.append('catalogue_id', selectedPdfId);
  
    Http.PostAPI(apis.delete_Pdf, formData)
      .then((res) => {
        console.log("delete catalogue api working",res);

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
      <DialogTitle>Delete PDF
        <IconButton aria-label="close" onClick={handleClose}
          style={{ color: "black", right: "10px", top: "10px", position: "absolute" }}>
          <CloseIcon />
        </IconButton>
      </DialogTitle>
      <DialogContent>
        <DialogContentText>Are you sure you want to delete the PDF file?</DialogContentText>
      </DialogContent>
      <DialogActions>
        <Button onClick={()=>handleConfirmDelete (selectedPdfId)} color="primary">
          Delete
        </Button>
      </DialogActions>
    </Dialog>
  );
}