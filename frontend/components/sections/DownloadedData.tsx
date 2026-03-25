"use client";

import React, { useRef, useState } from "react";
import { GreenButton, Text } from "../ui/Typography";
import Input from "../ui/Input";
import TextArea from "../ui/TextArea";

import axios from "axios";
import Swal from "sweetalert2";
import { useRouter } from "next/navigation";

// import ReCAPTCHA from "react-google-recaptcha";
import dynamic from "next/dynamic";
// Dynamically import ReCAPTCHA to avoid SSR issues
const ReCAPTCHA = dynamic(
  () => import("react-google-recaptcha").then((mod) => mod.default),
  { ssr: false }
);

const DownloadedData = () => {
  const [formData, setFormData] = useState({
    firstname: "",
    lastname: "",
    email: "",
    organisation: "",
    designation: "",
    country: "",
    purpose: "",
  });

  const [loading, setLoading] = useState(false);

  const handleChange = (
    e: React.ChangeEvent<
      HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement
    >
  ) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const router = useRouter();
  // const [captchaToken, setCaptchaToken] = useState(null);
  const recaptchaRef = useRef<any>(null);
  const [captchaToken, setCaptchaToken] = useState<string | null>(null);

  const handleCaptchaChange = (token: any) => {
    setCaptchaToken(token);
  };

  // Handle Form Submission with correct typing
  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setLoading(true);

    if (!captchaToken) {
      // alert("Please complete the reCAPTCHA.");
      Swal.fire("Error", "Please complete the reCAPTCHA to proceed.", "error");
      setLoading(false);
      return;
    }

    try {
      // Basic Frontend Validation
      const data = { ...formData, captchaToken };

      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/download-facilities`,
        data
      );

      if (response.data.success) {
        console.log("Form submitted successfully:", response.data);
        const token = response.data?.data?.token; // Assuming the token is returned in the response

        // After the success message is shown
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "success",
          title: "Request Submitted!",
          text: "Your download request was received. Please check your email for the verification code to proceed.",
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });

        // Reset form fields
        setFormData({
          firstname: "",
          lastname: "",
          email: "",
          organisation: "",
          designation: "",
          country: "",
          purpose: "",
        });

        // ✅ Reset reCAPTCHA
        recaptchaRef.current?.reset();
        setCaptchaToken(null);

        if (token) {
          setTimeout(() => {
            // window.location.href = `${process.env.NEXT_PUBLIC_BACKEND_URL}/validate-token?token=${token}`;
            window.location.href = `${process.env.NEXT_PUBLIC_BACKEND_URL}/validate-token`;
          }, 3000);
        }

        // router.push(`/verify-download?email=${formData.email}`);
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops!",
          text: response.data.message || "Something went wrong!",
        });
      }
    } catch (error: unknown) {
      if (axios.isAxiosError(error)) {
        console.error("Error response:", error.response?.data);
        Swal.fire({
          icon: "error",
          title: "Submission Failed",
          html:
            `<ul style="text-align: left; padding-left: 20px;">
            ${Object.values(error.response?.data?.errors || {})
              .flat()
              .map((message) => `<li>${message}</li>`)
              .join("")}
            </ul>` || "Please check your input fields.",
        });
      } else {
        console.error("Unexpected error:", error);
        Swal.fire({
          icon: "error",
          title: "Unknown Error",
          text: "Something went wrong. Please try again.",
        });
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="w-full lg:w-[900px] mx-auto lg:pt-32">
      <div className="mx-2 lg:mx-0">
        <Text className="text-2xl font-bold mb-2">Data Downloads</Text>

        <div className="bg-green-100 text-green-800 p-3 rounded-md mt-4 font-medium">
          <Text className="text-[#363636] bg-green-100 text-green-800 ">
            Thank you for your interest in Nigeria HFR data, kindly sign our
            guest form to download the data.
          </Text>
        </div>
      </div>

      <div className="border shadow-sm rounded-lg p-8 mt-4">
        <form className="flex flex-col gap-[1rem]" onSubmit={handleSubmit}>
          <div className="flex flex-col lg:flex-row gap-[2rem]">
            <Input
              label="First name"
              placeholder="First name"
              name="firstname"
              value={formData.firstname}
              onChange={handleChange}
            />
            <Input
              label="Last name"
              placeholder="Last name"
              name="lastname"
              value={formData.lastname}
              onChange={handleChange}
            />
          </div>
          <div className="flex flex-col lg:flex-row gap-[2rem]">
            <Input
              label="Email Address"
              type="email"
              placeholder="e.g ade@gmail.com"
              name="email"
              value={formData.email}
              onChange={handleChange}
            />
            <Input
              label="Organization"
              placeholder="Organization"
              name="organisation"
              value={formData.organisation}
              onChange={handleChange}
            />
          </div>
          <div className="flex flex-col lg:flex-row gap-[2rem]">
            <Input
              label="Designation"
              type="text"
              placeholder="Designation"
              name="designation"
              value={formData.designation}
              onChange={handleChange}
            />
            <Input
              label="Country"
              placeholder="e.g Nigeria"
              name="country"
              value={formData.country}
              onChange={handleChange}
            />
          </div>
          <TextArea
            label="Purpose"
            placeholder="Intended usage of data"
            name="purpose"
            value={formData.purpose}
            onChange={handleChange}
          />

          <div
            className=""
            style={{ transform: "scale(0.91)", transformOrigin: "0 0" }}
          >
            <ReCAPTCHA
              sitekey="6LepSyIsAAAAAAyBL7ZaShYGj4vFxk3X-i7gJYc9"
              onChange={handleCaptchaChange}
            />
          </div>

          <div className="flex justify-end">
            <GreenButton
              className="bg-[#5BBA62] w-full"
              // type="submit"
              // disabled={loading}
            >
              {loading ? "Submitting..." : "Submit Request"}
            </GreenButton>

            {/* <button
              type="submit"
              disabled={loading}
              className="bg-[#5BBA62] px-4 py-2 rounded"
            >
              <GreenButton>
                {loading ? "Submitting..." : "Submit request"}
              </GreenButton>
            </button> */}
          </div>
        </form>
      </div>
    </div>
  );
};

export default DownloadedData;
