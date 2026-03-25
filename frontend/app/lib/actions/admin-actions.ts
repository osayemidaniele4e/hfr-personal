"use server";
import { revalidatePath, revalidateTag } from "next/cache";
import { cookies } from "next/headers";

const baseUrl = process.env.NEXT_PUBLIC_BACKEND_API;

// Admin Login
export async function AdminLogin(data: any) {
  const res = await fetch(baseUrl + "admin/auth/login", {
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

// delete_user
export async function DeleteUsers(id: any, type: any) {
  const res = await fetch(baseUrl + `admin/delete_user/${id}?type=${type}`, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
    },
  });
  revalidateTag("allTalent");
  revalidateTag("allCompany");
  const refer = await res.json();
  return refer;
}

// get all talent status
export async function GetTalentStats() {
  const res = await fetch(baseUrl + `talent/profile/get_talent_stats`);
  const refer = await res.json();
  return refer;
}

// get all referral request status
export async function getReferralStats() {
  const res = await fetch(baseUrl + `talent_referral/get_referral_stats`);
  const refer = await res.json();
  return refer;
}
// get all company status
export async function getCompanyStats() {
  const res = await fetch(baseUrl + `company/profile/get_company_stats`, {
    next: { revalidate: 0 },
  });
  const refer = await res.json();
  return refer;
}

//get all referrals
export const getAllReferral = async () => {
  let res = await fetch(`${baseUrl}talent_referral/get_all_referrals`, {
    // next: { revalidate: 0, tags: ["allCompany"] },
  });
  const data = await res.json();
  return data;
};

//get all companies
export const getAllCompany = async () => {
  let res = await fetch(`${baseUrl}company/get_all_companies`, {
    next: { revalidate: 0, tags: ["allCompany"] },
  });
  const data = await res.json();
  return data;
};

//get all talents
export const GetAlltalentData = async () => {
  let res = await fetch(`${baseUrl}talent/get_all_talents`, {
    next: { tags: ["allTalent"] },
  });
  const data = await res.json();
  return data;
};

// get Talent That Match
export const getTalentThatMatch = async (requestid: any) => {
  let res = await fetch(
    `${baseUrl}talent_request/get_talents_that_match_request/${requestid}`,
    {
      // next: { tags: ["allTalent"] },
    }
  );
  const data = await res.json();
  return data;
};

export const getTalentRequest = async () => {
  let res = await fetch(`${baseUrl}talent_request/get_all_talent_requests`, {
    next: { tags: ["allTalentRequest"] },
  });
  const data = await res.json();
  return data;
};

export const getAllNotifications = async () => {
  let res = await fetch(`${baseUrl}admin/auth/get_all_notifications?`, {
    next: { revalidate: 0 },
  });
  const data = await res.json();
  return data;
};
//create referrals
export async function createReferral(data: any) {
  const res = await fetch(baseUrl + "talent_referral/create_referral", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });
  const refer = await res.json();
  revalidatePath("/admin-dashboard/talent-request");
  return refer;
}

export async function exportReferral(data: any) {
  try {
    const response = await fetch(baseUrl + "talent_referral/create_referral", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        // Add other headers if needed
      },
      body: JSON.stringify(data),
    });

    if (!response.ok) {
      throw new Error("Failed to export referrals");
    }

    const blob = await response.blob();
    const headers = response.headers;
    return { blob, headers };
  } catch (error) {
    console.error("Error exporting referrals:", error);
    throw error;
  }
}
