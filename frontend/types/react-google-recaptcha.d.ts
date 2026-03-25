// src/types/react-google-recaptcha.d.ts (or any other location you prefer)
declare module 'react-google-recaptcha' {
    import { ComponentType } from 'react';
  
    interface ReCAPTCHAProps {
      sitekey: string;
      onChange: (value: string) => void;
    }
  
    const ReCAPTCHA: ComponentType<ReCAPTCHAProps>;
    export default ReCAPTCHA;
  }
  