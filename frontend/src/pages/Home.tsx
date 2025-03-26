import React from "react";
import favicon from "../assets/favicon.ico";
import { Link as RouterLink } from "react-router-dom";
import {
  AppBar,
  Toolbar,
  Typography,
  Container,
  Grid,
  Card,
  CardContent,
  CardActions,
  Button,
  Box,
} from "@mui/material";

// อาร์เรย์ของ services พร้อม path และชื่อที่แสดง
const services = [
  { name: "Users", path: "/users" },
  { name: "Marital Status Type", path: "/v1/maritalstatustype" },
  { name: "Marital Status", path: "/v1/maritalstatus" },
  { name: "Person Name Type", path: "/v1/personnametype" },
  {
    name: "Physical Characteristic Type",
    path: "/v1/physicalcharacteristictype",
  },
  { name: "Country", path: "/v1/country" },
  { name: "Person Name", path: "/v1/personname" },
  { name: "Citizenship", path: "/v1/citizenship" },
  { name: "Passport", path: "/v1/passport" },
  { name: "Person", path: "/v1/person" },
  { name: "Party Type", path: "/v1/partytype" },
  { name: "Party Classification", path: "/v1/partyclassification" },
  { name: "Legal Organization", path: "/v1/legalorganization" },
  { name: "Physical Characteristic", path: "/v1/physicalcharacteristic" },
  { name: "Informal Organization", path: "/v1/informalorganization" },
  { name: "Ethnicity", path: "/v1/ethnicity" },
  { name: "Income Range", path: "/v1/incomerange" },
  { name: "Industry Type", path: "/v1/industrytype" },
  { name: "Employee Count Range", path: "/v1/employeecountrange" },
  { name: "Minority Type", path: "/v1/minoritytype" },
  { name: "Classify by EEOC", path: "/v1/classifybyeeoc" },
  { name: "Classify by Income", path: "/v1/classifybyincome" },
  { name: "Classify by Industry", path: "/v1/classifybyindustry" },
  { name: "Classify by Size", path: "/v1/classifybysize" },
  { name: "Classify by Minority", path: "/v1/classifybyminority" },
];

export default function Home() {
  return (
    <>
      <Box sx={{ flexGrow: 1 }}>
        {/* ส่วนหัว (AppBar) */}
        {/* Header (AppBar) */}
        <AppBar position="static">
          <Toolbar>
            <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
              CRUD Services Dashboard
            </Typography>
          </Toolbar>
        </AppBar>

        {/* คอนเทนเนอร์หลัก */}
        {/* Main container */}
        <Container maxWidth="lg" sx={{ mt: 4, mb: 4 }}>
          <Typography variant="h4" gutterBottom align="center">
            <img src="/public/vite.svg" alt="vite" />
            Party Model Admin
            <img src="/public/favicon.ico" alt="favicon" />
          </Typography>
          <Typography variant="subtitle1" gutterBottom align="center">
            Select a service below to manage its data
          </Typography>

          {/* Grid สำหรับจัดเรียงการ์ด */}
          {/* Grid for arranging cards */}
          <Grid container spacing={3} sx={{ mt: 2 }}>
            {services.map((service) => (
              <Grid item xs={12} sm={6} md={4} key={service.path}>
                {/* การ์ดสำหรับแต่ละ service */}
                {/* Card for each service */}
                <Card
                  sx={{
                    height: "100%",
                    display: "flex",
                    flexDirection: "column",
                  }}
                >
                  <CardContent sx={{ flexGrow: 1 }}>
                    <Typography variant="h6" component="div">
                      {service.name}
                    </Typography>
                    <Typography variant="body2" color="text.secondary">
                      Manage {service.name} data
                    </Typography>
                  </CardContent>
                  <CardActions>
                    {/* ปุ่มลิงก์ไปยังหน้า CRUD ของ service */}
                    {/* Button linking to the CRUD page of the service */}
                    <Button
                      component={RouterLink}
                      to={service.path}
                      size="small"
                      color="primary"
                    >
                      Go to {service.name}
                    </Button>
                  </CardActions>
                </Card>
              </Grid>
            ))}
          </Grid>
        </Container>
        <img src="/public/party_model.png" alt="diagram" width={"100%"}/>

      </Box>
    </>
  );
}
