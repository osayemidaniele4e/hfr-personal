"use server";

import { cookies } from "next/headers";

export async function setCookie(key: string, arg: any) {
  cookies().set(key, arg, {
    secure: true,
    httpOnly: true,
    expires: Date.now() + 24 * 60 * 60 * 1000 * 3, // expires in 3 days
    path: "/",
    sameSite: "strict",
  });
}

export async function getCookie(key: any) {
  cookies().get(key);
}

export async function deleteCookie(key: string) {
  cookies().delete(key);
}
