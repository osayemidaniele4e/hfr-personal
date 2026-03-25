"use server";
import { cookies } from "next/headers";
import { signIn, auth } from "../auth";
import { revalidatePath, revalidateTag } from "next/cache";
import next from "next";

const baseUrl = process.env.NEXT_PUBLIC_BACKEND_API;
console.log("baseUrl", baseUrl);


export async function SignIn() {
  return await signIn("google");
}
//Company Authetication
//company Register
export async function CompanyRegister(data: any) {
  const res = await fetch(baseUrl + "company/auth/signup", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}

//company Login
export async function companyLoginAction(data: any) {
  const res = await fetch(baseUrl + "company/auth/login", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });
  const credentials = await res.json();
  cookies().set("session", JSON.stringify(credentials), {
    secure: true,
    httpOnly: true,
    expires: new Date(Date.now() + 24 * 60 * 60 * 1000 * 3), // expires in 3 days
    path: "/",
    sameSite: "strict",
  });

  return credentials;
}

//company send Otp
export async function RegisterUser(data: any) {
  console.log("this endpoint running");
  const res = await fetch(baseUrl + "user/register-user", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}
export async function VerifyPayment(data: any) {
  console.log("verify payment run");

  try {
    const res = await fetch(baseUrl + "order/verify", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    // Check if the request was successful
    if (!res.ok) {
      const errorResponse = await res.json();
      throw new Error(errorResponse.message || "Failed to verify payment");
    }

    const response = await res.json();
    return response;
  } catch (error: any) {
    console.error("Error during payment verification:", error);
    throw error; // Re-throw the error to be caught in the calling function
  }
}
export async function VerifyStripePayment(data: any) {
  console.log("verify payment run");
  console.log("verify data", data);

  try {
    const res = await fetch(baseUrl + "order/create-payment-intent", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    // Check if the request was successful
    if (!res.ok) {
      const errorResponse = await res.json();
      throw new Error(errorResponse.message || "Failed to verify payment");
    }

    const response = await res.json();
    return response;
  } catch (error: any) {
    console.error("Error during payment verification:", error);
    throw error; // Re-throw the error to be caught in the calling function
  }
}

//company validate otp
export async function CompanyValidateOtp(data: any) {
  const res = await fetch(baseUrl + "company/auth/validate_otp", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}

//company forgot Password SendOtp
export async function companyforgotPasswordSendOtp(data: any) {
  const res = await fetch(baseUrl + "company/auth/forgot_password/send_otp", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}
//company reset
export async function companyForgotPasswordReset(data: any) {
  const res = await fetch(baseUrl + "company/auth/forgot_password", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}

// Requests

//get_request talent request
export const talentData = async (companyid: any) => {
  let res = await fetch(
    `${baseUrl}talent_request/get_talent_requests/${companyid}`,
    {
      next: {
        revalidate: 0,
      },
    }
  );
  const data = await res.json();
  return data;
};

//create talent request
export const CreateRequest = async (data: any) => {
  let res = await fetch(`${baseUrl}talent_request/create_request/`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });
  const response = await res.json();
  revalidatePath("/admin-dashboard/talent-request");
  revalidateTag("notification");
  return response;
};
//update talent request
export const UpdateRequest = async (data: any, requestid: any) => {
  let res = await fetch(
    `${baseUrl}talent_request/update_request/${requestid}`,
    {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    }
  );
  const response = await res.json();
  return response;
};
//update Referral Status
export const updateReferralStatus = async (data: any, requestid: any) => {
  let res = await fetch(
    `${baseUrl}talent_referral/update_referral_status/${requestid}`,

    {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    }
  );
  const response = await res.json();
  revalidateTag("suggestedTalents");
  return response;
};
// get Talent Request Referrals
export const getTalentRequestReferrals = async (requestid: any) => {
  let res = await fetch(
    `${baseUrl}talent_referral/get_talent_request_referral/${requestid}`,
    {
      next: {
        revalidate: 0,

        tags: ["suggestedTalents"],
      },
    }
  );
  const data = await res.json();
  return data;
};
