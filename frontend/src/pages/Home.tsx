import React from "react";
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
  styled,
  ThemeProvider,
  createTheme,
} from "@mui/material";
import ArrowForwardIcon from "@mui/icons-material/ArrowForward";

// อาร์เรย์ของ services
const services = [
  { name: "Users", path: "/users" },
  { name: "Marital Status Type", path: "/v1/maritalstatustype" },
  { name: "Marital Status", path: "/v1/maritalstatus" },
  { name: "Person Name Type", path: "/v1/personnametype" },
  { name: "Physical Characteristic Type", path: "/v1/physicalcharacteristictype" },
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

// สร้าง theme สีน้ำเงิน
const theme = createTheme({
  palette: {
    primary: {
      main: "#1976d2", // สีน้ำเงินหลักของ MUI
      light: "#42a5f5",
      dark: "#1565c0",
    },
    background: {
      default: "#f5f7fa", // พื้นหลังสีเทาอ่อน
    },
  },
  typography: {
    fontFamily: "Roboto, sans-serif",
    h4: { fontWeight: 700 },
    subtitle1: { color: "#555" },
  },
});

// Styled Components
const StyledAppBar = styled(AppBar)({
  background: "linear-gradient(90deg, #1976d2 0%, #42a5f5 100%)",
  boxShadow: "0 4px 12px rgba(0, 0, 0, 0.1)",
});

const StyledCard = styled(Card)(({ theme }) => ({
  height: "100%",
  display: "flex",
  flexDirection: "column",
  transition: "transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out",
  "&:hover": {
    transform: "translateY(-8px)",
    boxShadow: "0 8px 24px rgba(0, 0, 0, 0.15)",
  },
}));

const StyledButton = styled(Button)(({ theme }) => ({
  textTransform: "none",
  fontWeight: 600,
  padding: "6px 12px",
  "&:hover": {
    backgroundColor: theme.palette.primary.light,
  },
}));

export default function Home() {
  return (
    <ThemeProvider theme={theme}>
      <Box sx={{ flexGrow: 1, minHeight: "100vh", backgroundColor: "background.default" }}>
        {/* AppBar */}
        <StyledAppBar position="static">
          <Toolbar>
            <Typography
              variant="h6"
              component="div"
              sx={{ flexGrow: 1, fontWeight: 700, letterSpacing: ".1rem" }}
            >
              Party Model Dashboard
            </Typography>
          </Toolbar>
        </StyledAppBar>

        {/* Main Content */}
        <Container maxWidth="lg" sx={{ mt: 6, mb: 6 }}>
          {/* Header Section */}
          <Box sx={{ textAlign: "center", mb: 4 }}>
            <Typography
              variant="h4"
              gutterBottom
              sx={{ color: "primary.main", display: "flex", alignItems: "center", justifyContent: "center" }}
            >
              <img src="/public/vite.svg" alt="vite" style={{ width: 32, marginRight: 8 }} />
              Party Model Admin
              <img src="/public/favicon.ico" alt="favicon" style={{ width: 32, marginLeft: 8 }} />
            </Typography>
            <Typography variant="subtitle1" gutterBottom>
              Explore and manage your services effortlessly
            </Typography>
          </Box>

          {/* Services Grid */}
          <Grid container spacing={3}>
            {services.map((service) => (
              <Grid item xs={12} sm={6} md={4} key={service.path}>
                <StyledCard>
                  <CardContent sx={{ flexGrow: 1 }}>
                    <Typography variant="h6" component="div" color="primary.main">
                      {service.name}
                    </Typography>
                    <Typography variant="body2" color="text.secondary">
                      Manage {service.name.toLowerCase()} data efficiently
                    </Typography>
                  </CardContent>
                  <CardActions sx={{ p: 2 }}>
                    <StyledButton
                      component={RouterLink}
                      to={service.path}
                      size="small"
                      color="primary"
                      endIcon={<ArrowForwardIcon />}
                    >
                      Go to {service.name}
                    </StyledButton>
                  </CardActions>
                </StyledCard>
              </Grid>
            ))}
          </Grid>
        </Container>

        {/* Footer Image */}
        <Box sx={{ px: 2, pb: 4 }}>
          <img
            src="/public/party_model.png"
            alt="Party Model Diagram"
            style={{ width: "100%", maxWidth: "1200px", display: "block", margin: "0 auto" }}
          />
        </Box>
      </Box>
    </ThemeProvider>
  );
}