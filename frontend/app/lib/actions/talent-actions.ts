"use server";

import { revalidatePath, revalidateTag } from "next/cache";
import { cookies } from "next/headers";
import { getSession } from "./session";

const baseUrl = process.env.NEXT_PUBLIC_BACKEND_API;

//talent Register
export async function TalentRegister(data: any) {
  const res = await fetch(baseUrl + "talent/auth/signup", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}

//talent login
export async function TalentLoginIn(data: any) {
  const res = await fetch(baseUrl + "talent/auth/login", {
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

//talent send Otp
export async function TalentSendOtp(data: any) {
  const res = await fetch(baseUrl + "talent/auth/signup/send_otp", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}

//talent validate otp
export async function TalentValidateOtp(data: any) {
  const res = await fetch(baseUrl + "talent/auth/validate_otp", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}
//talent forgot Password SendOtp
export async function talentforgotPasswordSendOtp(data: any) {
  const res = await fetch(baseUrl + "talent/auth/forgot_password/send_otp", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}
//talent reset
export async function talentForgotPasswordReset(data: any) {
  const res = await fetch(baseUrl + "talent/auth/forgot_password", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const response = await res.json();
  return response;
}
//get talent profile
export const TalentProfile = async (talentid: any) => {
  let res = await fetch(`${baseUrl}talent/profile/get_details/${talentid}`, {
    next: {
      revalidate: 0,
    },
  });
  const data = await res.json();
  return data;
};

//update talent details
export async function UpdateTalentDetails(formData: FormData) {
  const res = await fetch(baseUrl + "talent/profile/update_details", {
    method: "PATCH",
    // headers: {
    //   "Content-Type": "application/json",
    // },
    body: formData,
  });
  const json = await res.json();
  revalidatePath("/admin-dashboard/home");
  revalidatePath("/talent-dashboard/home");
  return json;
}

//up_load_talents
export async function UploadTalentDetails(data: any) {
  const res = await fetch(baseUrl + "talent/profile/upload_details", {
    method: "POST",
    // headers: {
    //   "Content-Type": "application/json",
    // },
    body: data,
  });
  const json = await res.json();
  revalidatePath("/talent-dashboard/home");
  revalidateTag("talentprofile");
  return json;
}
