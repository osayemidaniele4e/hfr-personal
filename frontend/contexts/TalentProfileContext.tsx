"use client";
import { getSession } from "@/app/lib/actions/session";
import { TalentProfile } from "@/app/lib/actions/talent-actions";
import { profileContextType } from "@/types/authType";
import { redirect } from "next/navigation";
import { createContext, useContext, useState } from "react";

const TalentProfileContext = createContext<profileContextType | null>(null);

async function TalentProfileProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await getSession();
  const talentid = session?.talentData?.id;
  const data = await TalentProfile(talentid);

  if (!session) {
    redirect("/talent-login");
  }

  return (
    <TalentProfileContext.Provider value={{ data, session }}>
      {children}
    </TalentProfileContext.Provider>
  );
}

export { TalentProfileProvider, TalentProfileContext };
