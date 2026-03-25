import ContactPage from "@/components/sections/contact/Contact";
import React from "react";
import { createPageMetadata } from "@/app/lib/metadata";

export const metadata = createPageMetadata(
  "Contact Us",
  "Get in touch with the National Health Facility Registry (NHFR). Contact our team for inquiries, feedback, and support.",
  "NHFR, contact NHFR, Nigeria health facilities, support, health data, healthcare registry",
  "/contact-us"
);


const Contact = () => {
  return (
    <div>
      <ContactPage />
    </div>
  );
};

export default Contact;
