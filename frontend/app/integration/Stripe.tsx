"use client";

import { Elements } from "@stripe/react-stripe-js";
import { loadStripe, StripeElementsOptions } from "@stripe/stripe-js";
import CheckoutForm from "./CheckoutForm";
import { useCallback, useEffect, useState } from "react";
import { toast } from "react-toastify";
import { VerifyStripePayment } from "../lib/actions/company-actions";
import useUtilityService, { CourseData } from "@/helpers/UtilityService";

// Make sure to call `loadStripe` outside of a component’s render to avoid
// recreating the `Stripe` object on every render.

const publishableKey = process.env.NEXT_PUBLIC_STRIPE_WEBHOOK_SECRET;
const stripePromise = loadStripe(publishableKey as string);

export default function Stripe() {
  const [message, setMessage] = useState("Initializing checkout...");
  const [clientSecret, setClientSecret] = useState("");
  const { getCourseFromLocalStorage } = useUtilityService();
  const data = getCourseFromLocalStorage() as CourseData;

  // const handleStripePayment = useCallback(async () => {
  //   try {
  //     const response = await VerifyStripePayment({
  //       description: data?.course,
  //       price: 50,
  //     });
  //     if (response) {
  //       setClientSecret(response?.clientSecret);
  //     }
  //   } catch (error: any) {
  //     toast.error(error.message);
  //   }
  // }, []);

  // useEffect(() => {
  //   handleStripePayment();
  // }, [handleStripePayment]);

  const appearance = {
    theme: "stripe" as const,
  };

  const options: StripeElementsOptions = {
    clientSecret,
    appearance,
  };

  return (
    <div className="flex flex-col w-full md:max-w-[800px] mx-auto">
      <div className="container text-center">
        {!clientSecret && <h3>{message}</h3>}
      </div>

      {clientSecret && (
        <Elements stripe={stripePromise} options={options}>
          <CheckoutForm />
        </Elements>
      )}
    </div>
  );
}
