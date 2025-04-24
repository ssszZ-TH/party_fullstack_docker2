// styles/theme.ts
import { createTheme } from '@mui/material/styles';

export const lightTheme = createTheme({
  palette: {
    mode: 'light',
    primary: {
      main: '#86B5EA',
      dark: '#273D54',
    },
    secondary: {
      main: '#dc004e',
    },
    error: {
      main: '#FFB3BA',
    },
    background: {
      default: '#E7F1FC',
    },
  },
});

export const darkTheme = createTheme({
  palette: {
    mode: 'dark',
    primary: {
      main: '#90caf9',
    },
    secondary: {
      main: '#f48fb1',
    },
  },
});