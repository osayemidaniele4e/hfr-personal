import React, { useEffect, useState } from "react";
import {
  PaymentElement,
  useElements,
  useStripe,
} from "@stripe/react-stripe-js";
import { StripePaymentElementOptions } from "@stripe/stripe-js";
import { toast } from "react-toastify";
import { useRouter } from "next/navigation";
import Spinner from "@/components/ui/Spinner";
import { useDisclosure } from "@chakra-ui/react";
import SuccessMessageModal from "@/components/ui/SuccessMessageModal";

const CheckoutForm: React.FC = () => {
  const [message, setMessage] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [openSuccess, setOpenSuccess] = useState(false);
  const { isOpen, onOpen, onClose } = useDisclosure();

  const stripe = useStripe();
  const elements = useElements();
  const router = useRouter();

  const paymentElementOptions: StripePaymentElementOptions = {
    // Add any specific options here
    layout: "tabs",
  };

  const handleViewDashboard = () => {
    router.push("/");
  };

  useEffect(() => {
    if (!stripe) return;

    const clientSecret = new URLSearchParams(window.location.search).get(
      "payment_intent_client_secret"
    );

    if (!clientSecret) return;

    // You can add additional logic here if needed
  }, [stripe]);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setMessage(null);
    console.log("Form submitted");

    if (!stripe || !elements) {
      console.log("Stripe or elements not initialized");
      return;
    }

    setIsLoading(true);

    try {
      console.log("Attempting to confirm payment");
      const { error, paymentIntent } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: "http://localhost:3006/checkout-success",
        },
        redirect: "if_required",
      });

      onOpen();

      if (error) {
        toast.error(error.message || "An error occurred");
        setMessage(error.message || "An error occurred");
      } else if (paymentIntent && paymentIntent.status === "succeeded") {
        toast.success("Payment successful");
        setOpenSuccess(true);
      } else {
        console.log("Unexpected payment state:", paymentIntent?.status);
      }
    } catch (err) {
      toast.error("An unexpected error occurred");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div>
      <form onSubmit={handleSubmit} className="max-h-96 overflow-y-auto p-4">
        <h3 className="text-center font-bold mb-4">Stripe Checkout</h3>
        <PaymentElement options={paymentElementOptions} />
        <div className="mt-4">
          <button
            disabled={isLoading || !stripe || !elements}
            className="btn text-[#fff] rounded-full bg-[#04BFFF] hover:bg-[#2563EB] w-[100%]  md:w-[200px]  border-0 block mx-auto mt-2"
            type="submit"
          >
            {isLoading ? <Spinner /> : "Pay now"}
          </button>
        </div>
        {message && <div className="mt-2 text-red-500">{message}</div>}
      </form>

      <SuccessMessageModal handleViewDashboard={handleViewDashboard} isOpen={isOpen} onClose={onClose}/>
    </div>
  );
};

export default CheckoutForm;
