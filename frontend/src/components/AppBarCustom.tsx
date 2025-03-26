import * as React from "react";
import AppBar from "@mui/material/AppBar";
import Box from "@mui/material/Box";
import Toolbar from "@mui/material/Toolbar";
import IconButton from "@mui/material/IconButton";
import Typography from "@mui/material/Typography";
import Menu from "@mui/material/Menu";
import MenuIcon from "@mui/icons-material/Menu";
import Container from "@mui/material/Container";
import Avatar from "@mui/material/Avatar";
import Button from "@mui/material/Button";
import Tooltip from "@mui/material/Tooltip";
import MenuItem from "@mui/material/MenuItem";
import AdbIcon from "@mui/icons-material/Adb";
import { styled } from "@mui/material/styles";

const pages = ["Go To Home"];
const settings = ["Profile", "Account", "Dashboard", "Logout"];

interface ResponsiveAppBarProps {
  title: string;
}

// สร้าง styled component สำหรับ AppBar
const StyledAppBar = styled(AppBar)(({ theme }) => ({
  background: "linear-gradient(90deg, #1976d2 0%, #42a5f5 100%)", // Gradient สีน้ำเงิน
  boxShadow: "0 4px 12px rgba(0, 0, 0, 0.1)", // เงาอ่อนๆ
}));

// สร้าง styled component สำหรับ Button
const StyledButton = styled(Button)(({ theme }) => ({
  color: "#fff",
  fontWeight: 600,
  padding: "6px 16px",
  "&:hover": {
    backgroundColor: "rgba(255, 255, 255, 0.1)",
    transform: "scale(1.05)", // ขยายเล็กน้อยเมื่อ hover
    transition: "all 0.2s ease-in-out",
  },
}));

// สร้าง styled component สำหรับ MenuItem
const StyledMenuItem = styled(MenuItem)(({ theme }) => ({
  "&:hover": {
    backgroundColor: theme.palette.primary.light,
    color: "#fff",
    transition: "background-color 0.2s ease-in-out",
  },
}));

function ResponsiveAppBar({ title }: ResponsiveAppBarProps) {
  const [anchorElNav, setAnchorElNav] = React.useState<null | HTMLElement>(null);
  const [anchorElUser, setAnchorElUser] = React.useState<null | HTMLElement>(null);

  const [titleText] = React.useState<string>(title || "...");

  const handleOpenNavMenu = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorElNav(event.currentTarget);
  };

  const handleOpenUserMenu = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorElUser(event.currentTarget);
  };

  const handleCloseNavMenu = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorElNav(null);
    const page = event.currentTarget.textContent;
    if (page === "Go To Home") {
      window.location.href = "/";
    }
  };

  const handleCloseUserMenu = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorElUser(null);
    console.log(event.currentTarget.textContent);
  };

  return (
    <StyledAppBar position="static">
      <Container maxWidth="xl">
        <Toolbar disableGutters>
          {/* Logo และ Title (Desktop) */}
          <AdbIcon sx={{ display: { xs: "none", md: "flex" }, mr: 1 }} />
          <Typography
            variant="h6"
            noWrap
            component="a"
            href="#app-bar-with-responsive-menu"
            sx={{
              mr: 2,
              display: { xs: "none", md: "flex" },
              fontFamily: "Roboto, sans-serif", // เปลี่ยนฟอนต์ให้ดูทันสมัย
              fontWeight: 700,
              letterSpacing: ".1rem", // ปรับ spacing
              color: "inherit",
              textDecoration: "none",
              "&:hover": {
                color: "#e3f2fd", // สีอ่อนลงเมื่อ hover
              },
            }}
          >
            {titleText}
          </Typography>

          {/* Menu Icon และ Title (Mobile) */}
          <Box sx={{ flexGrow: 1, display: { xs: "flex", md: "none" } }}>
            <IconButton
              size="large"
              aria-label="menu"
              aria-controls="menu-appbar"
              aria-haspopup="true"
              onClick={handleOpenNavMenu}
              color="inherit"
              sx={{ "&:hover": { backgroundColor: "rgba(255, 255, 255, 0.1)" } }}
            >
              <MenuIcon />
            </IconButton>
            <Menu
              id="menu-appbar"
              anchorEl={anchorElNav}
              anchorOrigin={{ vertical: "bottom", horizontal: "left" }}
              keepMounted
              transformOrigin={{ vertical: "top", horizontal: "left" }}
              open={Boolean(anchorElNav)}
              onClose={handleCloseNavMenu}
              sx={{
                display: { xs: "block", md: "none" },
                "& .MuiPaper-root": {
                  backgroundColor: "#1976d2", // สีเมนูให้เข้ากับ AppBar
                  color: "#fff",
                  borderRadius: "8px",
                  boxShadow: "0 4px 12px rgba(0, 0, 0, 0.2)",
                },
              }}
            >
              {pages.map((page) => (
                <StyledMenuItem key={page} onClick={handleCloseNavMenu}>
                  <Typography sx={{ textAlign: "center" }}>{page}</Typography>
                </StyledMenuItem>
              ))}
            </Menu>
          </Box>
          <AdbIcon sx={{ display: { xs: "flex", md: "none" }, mr: 1 }} />
          <Typography
            variant="h5"
            noWrap
            component="a"
            href="#app-bar-with-responsive-menu"
            sx={{
              mr: 2,
              display: { xs: "flex", md: "none" },
              flexGrow: 1,
              fontFamily: "Roboto, sans-serif",
              fontWeight: 700,
              letterSpacing: ".1rem",
              color: "inherit",
              textDecoration: "none",
            }}
          >
            {titleText}
          </Typography>

          {/* Navigation Buttons (Desktop) */}
          <Box sx={{ flexGrow: 1, display: { xs: "none", md: "flex" } }}>
            {pages.map((page) => (
              <StyledButton key={page} onClick={handleCloseNavMenu}>
                {page}
              </StyledButton>
            ))}
          </Box>

          {/* User Menu */}
          <Box sx={{ flexGrow: 0 }}>
            <Tooltip title="Open settings">
              <IconButton onClick={handleOpenUserMenu} sx={{ p: 0 }}>
                <Avatar
                  alt="User"
                  src="/static/images/avatar/2.jpg"
                  sx={{
                    width: 40,
                    height: 40,
                    border: "2px solid #fff", // ขอบขาวรอบ Avatar
                    "&:hover": { transform: "scale(1.1)", transition: "all 0.2s" },
                  }}
                />
              </IconButton>
            </Tooltip>
            <Menu
              sx={{
                mt: "45px",
                "& .MuiPaper-root": {
                  backgroundColor: "#fff",
                  borderRadius: "8px",
                  boxShadow: "0 4px 12px rgba(0, 0, 0, 0.2)",
                },
              }}
              id="menu-appbar"
              anchorEl={anchorElUser}
              anchorOrigin={{ vertical: "top", horizontal: "right" }}
              keepMounted
              transformOrigin={{ vertical: "top", horizontal: "right" }}
              open={Boolean(anchorElUser)}
              onClose={handleCloseUserMenu}
            >
              {settings.map((setting) => (
                <StyledMenuItem key={setting} onClick={handleCloseUserMenu}>
                  <Typography sx={{ textAlign: "center" }}>{setting}</Typography>
                </StyledMenuItem>
              ))}
            </Menu>
          </Box>
        </Toolbar>
      </Container>
    </StyledAppBar>
  );
}

export default ResponsiveAppBar;