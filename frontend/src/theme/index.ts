import { createTheme } from '@mui/material/styles'

/**
 * Palette taken from the brand assets in public/: the logomark blue and the
 * logotype near black. Everything else derives from those two.
 */
export const colors = {
  brand: '#2C4CFD',
  brandSoft: '#E9EDFF',
  panel: '#222222',
  chip: '#F1F1F3',
  chipHover: '#E4E4E8',
  border: '#E6E6E9',
  surfaceMuted: '#F7F7F8',
}

export const theme = createTheme({
  palette: {
    mode: 'light',
    primary: { main: colors.brand, contrastText: '#FFFFFF' },
    text: { primary: colors.panel, secondary: '#6B7280' },
    background: { default: '#FFFFFF', paper: '#FFFFFF' },
    divider: colors.border,
  },
  shape: { borderRadius: 10 },
  typography: {
    fontFamily: '"Poppins", "Inter", "Helvetica", Arial, sans-serif',
    h5: { fontWeight: 700 },
    h6: { fontWeight: 700, fontSize: '1.05rem' },
    button: { fontWeight: 700, textTransform: 'none' },
  },
  components: {
    MuiButton: {
      defaultProps: { disableElevation: true },
      styleOverrides: {
        root: { borderRadius: 8, paddingInline: 28, paddingBlock: 12 },
      },
    },
    MuiPaper: {
      styleOverrides: {
        outlined: { borderColor: colors.border },
      },
    },
  },
})
