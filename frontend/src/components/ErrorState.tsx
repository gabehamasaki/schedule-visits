import Alert from '@mui/material/Alert'
import AlertTitle from '@mui/material/AlertTitle'
import Button from '@mui/material/Button'

type ErrorStateProps = {
  title?: string
  message: string
  onRetry?: () => void
}

export default function ErrorState({ title = 'Algo deu errado', message, onRetry }: ErrorStateProps) {
  return (
    <Alert
      severity="error"
      variant="outlined"
      action={
        onRetry && (
          <Button color="inherit" size="small" onClick={onRetry} sx={{ px: 2, py: 0.5 }}>
            Tentar novamente
          </Button>
        )
      }
    >
      <AlertTitle>{title}</AlertTitle>
      {message}
    </Alert>
  )
}
