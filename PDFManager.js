import React, { useState , useEffect} from "react";
import AddPdf from "./AddPdf.js";
import DeletePdf from "./DeletePdf.js";
import { makeStyles } from "@material-ui/core/styles";
import { TableCell, TableRow } from "@material-ui/core";
import GridItem from "components/Grid/GridItem.js";
import Button from "components/CustomButtons/Button.js";
import GridContainer from "components/Grid/GridContainer.js";

import Table from "components/Table/Table.js";
import Card from "components/Card/Card.js";
import CardHeader from "components/Card/CardHeader.js";
import CardBody from "components/Card/CardBody.js";
import { Http } from "../../utills/Service";
import{apis} from "../../utills/WebConstants";

const styles = {
  cardCategoryWhite: {
    "&,& a,& a:hover,& a:focus": {
      color: "rgba(255,255,255,.62)",
      margin: "0",
      fontSize: "14px",
      marginTop: "0",
      marginBottom: "0",
    },
    "& a,& a:hover,& a:focus": {
      color: "#FFFFFF",
    },
  },
  cardTitleWhite: {
    color: "#FFFFFF",
    marginTop: "0px",
    minHeight: "auto",
    fontWeight: "300",
    fontFamily: "'Roboto', 'Helvetica', 'Arial', sans-serif",
    marginBottom: "3px",
    textDecoration: "none",
    "& small": {
      color: "#777",
      fontSize: "65%",
      fontWeight: "400",
      lineHeight: "1",
    },
  },
};

const useStyles = makeStyles(styles);

export default function PDFManager() {
  const classes = useStyles();
  const [data, setData] = useState([
    // {id:"1", title:"Acknowledgement", desc:"sample pdf", pdf:"https://www.africau.edu/images/default/sample.pdf"},
    // {id:"2", title:"Catalogue", desc:"test pdf", pdf:"https://www.orimi.com/pdf-test.pdf"},
    // {id:"3", title:"Contents", desc:"", pdf:"https://morth.nic.in/sites/default/files/dd12-13_0.pdf"},
    // // {"4", "6985447810", "0065", "0"},
    // // {"5", "7745129950", "8596", "1"},
    // // Add more data as needed
  ]);
 
  const [selectedPdf, setSelectedPdf] = useState('');
  const [showAddModal, setShowAddModal] = useState(false);
  const [showDeleteModal, setShowDeleteModal] = useState(false);
  const [selectedPdfId, setSelectedPdfId] = useState('');

  const handleOpenModal = () => {
    setShowAddModal(true);
  };

  const handleAddPdfModalClose = () => {
    setShowAddModal(false);
  };

  const handleCloseModal = () => {
    handleAddPdfModalClose();
    setShowDeleteModal(false);
  };

  const handleAddPdf = (pdfData) => {
    const newPdf = {
      id: (data.length+1).toString(),
      ...pdfData,
    };
    setData([...data, newPdf]);
    handleAddPdfModalClose();
  };

  const handleDeletePdf = (id, pdf) => {
    setSelectedPdfId(id);
    setSelectedPdf(pdf);
    setShowDeleteModal(true);
  };
 
  const confirmDeletePdf = () => {
    const updatedPdfData = data.filter((item) => item.id !== selectedPdfId);
    setData(updatedPdfData);
    setShowDeleteModal(false);
  };

  const getPdf_Manager =()=> {

    Http.GetAPI(apis.Pdf_Manager, null)
    .then((res) => {
      console.log("resppp ", res);
      if (res && res.data && res.data.status) {
        setData(res.data.data)
      } else {
        console.log("error ", res.data);
      }
    })
    .catch((err) => console.log("error ", err));
  };
  useEffect(() => {
    getPdf_Manager();
  }, []);

  // const fetchPdf_Manager= () => {
  //   Http.GetAPI(apis.Pdf_Manager, Data)
  //     .then((res) => {
  //       console.log('resppp ', res);
  //       if (res && res.data && res.data.status) {
  //         setData(res.data.data);
  //       } else {
  //         console.log('error ', res.data);
  //       }
  //     })
  //     .catch((err) => console.log('error ', err));
  // };  

  // useEffect(() => {
  //   fetchPdf_Manager();
  // }, []);
  

 

  return (
    <>
       <Button onClick={handleOpenModal} color="primary" style={{ position: 'absolute', right: '35px' }}>
        Add PDF
      </Button>
      <br /><br />
      <GridContainer>
        <GridItem xs={12} sm={12} md={12}>
          <Card>
            <CardHeader color="primary">
              <h4 className={classes.cardTitleWhite}>PDF Manager Table</h4>
              <p className={classes.cardCategoryWhite}>
                PDF Details
              </p>
            </CardHeader>
            <CardBody>
              <Table
                style={{ justifyContent: "center", alignItems: "center" }}
                tableHeaderColor="primary"
                tableHead={["ID", "Title", "Description", "PDF File", "Action"]}
                tableData={data.map((item) => [

                  item.id,
                  item.pdf_title,
                  item.pdf_desc,
                  <a href={item.pdf_url} target="_blank" rel="noopener noreferrer">{item.pdf_url}</a>,
                  (
                    <Button onClick={() => handleDeletePdf(item.id, item.pdf_url)} color="primary">
                      Delete
                    </Button>
                  )
                ])}
              />
            </CardBody>
          </Card>
        </GridItem>
      </GridContainer>
      
      <AddPdf
        open={showAddModal}
        handleClose={handleAddPdfModalClose}
        handleAddPdf={handleAddPdf}
        fullWidth={true}
        maxWidth="xs"
        getPdf_Manager= {getPdf_Manager}
      />

      <DeletePdf
        open={showDeleteModal}
        handleClose={handleCloseModal}
        handleDeletePdf={confirmDeletePdf}
        selectedPdfId={selectedPdfId}
        fullWidth={true}
        maxWidth="xs"
        getPdf_Manager= {getPdf_Manager}
      />
    </>
  );
}