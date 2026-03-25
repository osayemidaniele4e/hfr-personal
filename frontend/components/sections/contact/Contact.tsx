"use client";
import React, { ChangeEvent, FormEvent, useState } from "react";
import { Mail, Phone, MapPin, Clock } from "lucide-react";
import { Text } from "@/components/ui/Typography";
// import ReCAPTCHA from "react-google-recaptcha";

import dynamic from "next/dynamic";

const ReCAPTCHA = dynamic(
  () => import("react-google-recaptcha").then((mod) => mod.default),
  { ssr: false }
);

import Swal from "sweetalert2";
import axios from "axios";

const ContactPage = () => {
  const [formData, setFormData] = useState({
    full_name: "",
    email: "",
    subject: "",
    message: "",
  });

  const [loading, setLoading] = useState(false);
  const [captchaToken, setCaptchaToken] = useState(null);

  const handleChange = (
    e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleCaptchaChange = (token: any) => {
    setCaptchaToken(token);
  };

  // const handleSubmit = async (e) => {
  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!captchaToken) {
      // alert("Please complete the reCAPTCHA.");
      Swal.fire("Error", "Please complete the reCAPTCHA to proceed.", "error");
      setLoading(false);
      return;
    }

    // Basic Frontend Validation
    if (
      !formData.full_name ||
      !formData.email ||
      !formData.subject ||
      !formData.message
    ) {
      return Swal.fire("Error", "All fields are required!", "error");
    }

    setLoading(true);

    const data = { ...formData, captchaToken };

    try {
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/contact`,
        data
      );

      if (response.data.success) {
        Swal.fire("Success", response.data.success, "success");
        setFormData({ full_name: "", email: "", subject: "", message: "" });
      } else {
        Swal.fire("Error", response.data.message, "error");
      }
    } catch (error) {
      Swal.fire("Error", "Something went wrong. Try again later.", "error");
    }

    setLoading(false);
  };

  return (
    <div className="w-full">
      <div className="pt-2">
        <section className="lg:py-16 w-full  mt-[2rem] ">
          <div className="w-full lg:h-[100vh]">
            <div className="flex flex-col lg:flex-row gap-[4rem]">
              <div className="flex flex-col gap-[1rem] bg-[#F5F7FA] lg:w-[600px] px-16 py-16">
                <h2 className="text-1xl font-bold mb-">Contact Info</h2>
                <Text className="text-1xl font-bold mb-">
                  Federal Ministry of Health
                </Text>
                <Text className="mb-8">
                  Department of Health Planning, Research and Statistics.
                </Text>

                <div className="flex items-start mb-5">
                  <div>
                    <h3 className="font-semibold">Address</h3>
                    <p className="text-gray-600">
                      New Federal Secretariat Complex, Phase III, Ahmadu Bello
                      Way, Central Business District, FCT Abuja, Nigeria.
                    </p>
                  </div>
                </div>

                <div className="space-y-6 mb-5">
                  <div className="flex items-start">
                    <div>
                      <h3 className="font-semibold">Email</h3>
                      <p className="text-gray-600">hfr@health.gov.ng</p>
                    </div>
                  </div>
                  <div className="flex items-start">
                    <div>
                      <h3 className="font-semibold">Telephone</h3>
                      <p className="text-gray-600">+234 805 965 9211</p>
                      <p className="text-gray-600">+234 806 644 9855</p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="w-full mx-auto lg:w-[600px] p-12">
                <h2 className="text-1xl font-bold mb-8">
                  Send Your Message/Feedback
                </h2>

                <form className="space-y-4" onSubmit={handleSubmit}>
                  <div className="bg-yellow-100 text-yellow-800 border border-yellow-300 p-4 rounded-md mb-4 text-sm">
                    All fields are required. Please fill out the form
                    completely.
                  </div>
                  <div>
                    <label
                      htmlFor="name"
                      className="block text-sm font-medium text-gray-700 mb-1"
                    >
                      Full Name
                    </label>
                    <input
                      type="text"
                      name="full_name"
                      value={formData.full_name}
                      onChange={handleChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                      placeholder="Enter fullname"
                    />
                  </div>
                  <div>
                    <label
                      htmlFor="email"
                      className="block text-sm font-medium text-gray-700 mb-1"
                    >
                      Email
                    </label>
                    <input
                      type="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                      placeholder="Enter Email Address"
                    />
                  </div>
                  <div>
                    <label
                      htmlFor="subject"
                      className="block text-sm font-medium text-gray-700 mb-1"
                    >
                      Subject
                    </label>
                    <input
                      type="text"
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                      placeholder="Subject"
                    />
                  </div>
                  <div>
                    <label
                      htmlFor="message"
                      className="block text-sm font-medium text-gray-700 mb-1"
                    >
                      Message
                    </label>
                    <textarea
                      name="message"
                      rows={4}
                      value={formData.message}
                      onChange={handleChange}
                      className="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"
                      placeholder="Enter Your Message"
                    ></textarea>
                  </div>

                  <div
                    className=""
                    style={{ transform: "scale(0.91)", transformOrigin: "0 0" }}
                  >
                    <ReCAPTCHA
                      sitekey="6Lfjki4rAAAAACunZuwgvyq9j6QoOFOpTFnJDr3D"
                      onChange={handleCaptchaChange}
                    />
                  </div>

                  <button
                    type="submit"
                    className="bg-[#326F32] text-white px-6 py-3 rounded-md hover:bg-emerald-700 transition block w-full mx-auto"
                    disabled={loading}
                  >
                    {loading ? "Wait sending message..." : "Send Message"}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  );
};

export default ContactPage;
