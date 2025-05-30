import React from "react";
import { Link as RouterLink } from "react-router-dom";
import {
  Box,
  Container,
  Grid,
  Typography,
  Avatar,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Divider,
} from "@mui/material";
import {
  Person as PersonIcon,
  Settings as SettingsIcon,
  Info as AboutIcon,
  Storage as DatabaseIcon,
  School as TutorialIcon,
  ArrowForward as ArrowForwardIcon,
} from "@mui/icons-material";
import { useTheme } from "@mui/material/styles";

// Services data array
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

// Navigation items
const navItems = [
  { name: "Profile", icon: <PersonIcon />, path: "/profile" },
  { name: "Settings", icon: <SettingsIcon />, path: "/settings" },
  { name: "About", icon: <AboutIcon />, path: "/about" },
  { name: "Database", icon: <DatabaseIcon />, path: "/database" },
  { name: "Tutorial", icon: <TutorialIcon />, path: "/tutorial" },
];

export default function Home() {
  const theme = useTheme();

  return (
    <Box sx={{ display: "flex", minHeight: "100vh" }}>
      {/* Vertical Navigation Bar */}
      <Box
        sx={{
          width: 240,
          position: "fixed",
          height: "100vh",
          bgcolor: "primary.light",
          boxShadow: 3,
          zIndex: 10,
        }}
      >
        {/* Logo */}
        <Box sx={{ p: 2, textAlign: "center" }}>
          <img 
            src="/sphere_wire_frame.svg" 
            alt="Logo" 
            style={{ 
              width: "100%",
              objectFit: "contain" 
            }} 
          />
        </Box>

        <Divider />

        {/* Navigation Items */}
        <List>
          {navItems.map((item) => (
            <ListItem key={item.name} disablePadding>
              <ListItemButton 
                component={RouterLink} 
                to={item.path}
                sx={{
                  "&:hover": {
                    bgcolor: theme.palette.action.hover,
                  }
                }}
              >
                <ListItemIcon sx={{ minWidth: 40 }}>
                  {item.icon}
                </ListItemIcon>
                <ListItemText primary={item.name} />
              </ListItemButton>
            </ListItem>
          ))}
        </List>
      </Box>

      {/* Main Content Area */}
      <Box
        component="main"
        sx={{
          flexGrow: 1,
          ml: 30, // Match nav width + spacing
          position: "relative",
        }}
      >
        {/* Background Graphic */}
        <img
          src="/sphere_wire_frame.svg"
          alt="Background"
          style={{
            position: "fixed",
            top: 0,
            left: 0,
            // width: "100%",
            height: "100vh",
            objectFit: "cover",
            zIndex: -1,
            opacity: 0.2,
          }}
        />

        {/* Content Container */}
        <Container maxWidth="lg" sx={{ py: 4 }}>
          {/* Page Header */}
          <Box sx={{ textAlign: "center", mb: 4 }}>
            <Typography variant="h4" gutterBottom>
              Party Model Admin
            </Typography>
            <Typography variant="subtitle1" color="text.secondary">
              Explore and manage your services
            </Typography>
          </Box>

          {/* Services Grid */}
          <Box
            sx={{
              display: 'flex',
              flexWrap: 'wrap',
              gap: '12px', // Reduced gap between items
              justifyContent: 'flex-start',
            }}
          >
            {services.map((service) => (
              <Box
                key={service.path}
                component={RouterLink}
                to={service.path}
                sx={{
                  display: 'flex',
                  flexDirection: 'column',
                  alignItems: 'center',
                  width: '110px', // Compact width
                  height: '110px', // Compact height
                  textDecoration: 'none',
                  transition: 'transform 0.2s',
                  '&:hover': {
                    transform: 'scale(1.05)',
                  },
                }}
              >
                <Avatar
                  src={`/home_thumbnail/${service.name.toLowerCase().replace(/\s+/g, '-')}/thumbnail.jpg`}
                  sx={{
                    width: 80, // Smaller thumbnail
                    height: 80,
                    mb: 0.5, // Reduced margin
                  }}
                />
                <Typography
                  variant="body2"
                  align="center"
                  color="text.primary"
                  sx={{
                    fontWeight: 500,
                    fontSize: '0.75rem', // Smaller text
                    lineHeight: 1.2,
                    height: '28px', // Fixed height for text
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    width: '100%',
                    overflow: 'hidden',
                    textOverflow: 'ellipsis',
                  }}
                >
                  {service.name}
                </Typography>
              </Box>
            ))}
          </Box>
        </Container>
      </Box>
    </Box>
  );
}