import React, { useState, useEffect } from "react";
import { Modal, Box, Button, TextField, Typography, FormControl, InputLabel, Select, MenuItem } from "@mui/material";

interface typeOfFormData {
  id: number | null;
  fromdate: string;
  thrudate: string;
  person_id: number;
  country_id: number;
}

const style = {
  position: "absolute" as "absolute",
  top: "50%",
  left: "50%",
  transform: "translate(-50%, -50%)",
  width: 400,
  bgcolor: "background.paper",
  boxShadow: 24,
  p: 4,
};

interface typeOfDD{
  id: number;
  text: string;
}

interface typeofModalProps {
  open: boolean;
  onClose: () => void;
  initialDetail: typeOfFormData;
  onSubmit: (updatedCountry: typeOfFormData) => void;
  openModalFor: string;
  personDD: Array<typeOfDD>;
  countryDD: Array<typeOfDD>;
}

export default function Modal_citizenship({
  open,
  onClose,
  initialDetail,
  onSubmit,
  openModalFor,
  personDD,
  countryDD,
}: typeofModalProps) {
  const [formData, setFormData] = useState<typeOfFormData>({
    id: null,
    fromdate: "",
    thrudate: "",
    country_id: 0,
    person_id: 0,
  });

  // ใช้ useEffect เพื่ออัปเดต formData เมื่อ initialDetail เปลี่ยน
  useEffect(() => {
    // ตรวจสอบว่า initialDetail มีข้อมูลที่จะใช้
    console.log("form initial detail = ", initialDetail);
    setFormData(initialDetail);
  }, [initialDetail]);


  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | { name?: string; value: unknown }>
  ) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = () => {
    onSubmit(formData);
    onClose();
  };

  useEffect(() => {
    console.log("openModalFor = ", openModalFor);
  }, [openModalFor]);

  return (
    <Modal open={open} onClose={onClose}>
      <Box sx={style}>
        <Typography variant="h6" component="h2">
          Details
        </Typography>
        <TextField
          label="From Date in format YYYY-MM-DD"
          name="fromdate"
          value={formData.fromdate}
          onChange={handleChange}
          fullWidth
          margin="normal"
        />
        <TextField
          label="Thru Date in format YYYY-MM-DD"
          name="thrudate"
          value={formData.thrudate}
          onChange={handleChange}
          fullWidth
          margin="normal"
        />
        <FormControl fullWidth margin="normal">
          <InputLabel id="state-select-label">State</InputLabel>
          <Select
            labelId="state-select-label"
            name="person_id"
            value={formData.person_id || ""}
            onChange={handleChange}
          >
            {personDD.map((item) => (
              <MenuItem key={item.id} value={item.id}>
                {item.text}
              </MenuItem>
            ))}
          </Select>
        </FormControl>
        <FormControl fullWidth margin="normal">
          <InputLabel id="state-select-label">State</InputLabel>
          <Select
            labelId="state-select-label"
            name="country_id"
            value={formData.country_id || ""}
            onChange={handleChange}
          >
            {countryDD.map((item) => (
              <MenuItem key={item.id} value={item.id}>
                {item.text}
              </MenuItem>
            ))}
          </Select>
        </FormControl>

        <Button variant="contained" color="primary" onClick={handleSubmit}>
          Save
        </Button>
      </Box>
    </Modal>
  );
}

