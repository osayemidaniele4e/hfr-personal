"use server";
import { NextRequest, NextResponse } from "next/server";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";

// Destroy the session
export async function logout(path: string) {
  cookies().set("session", "", { expires: new Date(0) });
  redirect(path);
}

// gets session
export async function getSession() {
  const session = cookies().get("session")?.value;
  if (!session) return null;
  const parsedData = JSON.parse(session);
  return parsedData;
}

export async function updateSession(request: NextRequest) {
  const session = request.cookies.get("session")?.value;
  if (!session) return;

  // Refresh the session so it doesn't expire
  const parsed = session;
  const res = NextResponse.next();
  res.cookies.set({
    name: "session",
    value: parsed,
    httpOnly: true,
    expires: new Date(Date.now() + 24 * 60 * 60 * 1000 * 3),
  });

  return res;
}
