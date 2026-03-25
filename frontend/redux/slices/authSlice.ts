import { deleteCookie, setCookie } from "@/app/lib/cookie-setter";
import { createSlice } from "@reduxjs/toolkit";
import { UserType } from "@/types/authType";
export type AuthSliceType = {
  saveCredentials: any;
  auth?: any;
  user: UserType | undefined | null;
};
const isWindowDefined = typeof window !== "undefined";

const USER = isWindowDefined ? localStorage.getItem("USER") : null;

let parsedUser = null;

try {
  parsedUser = USER ? JSON.parse(USER) : null;
} catch (error) {
  // eslint-disable-next-line no-console
  console.error(`Failed to parse USER from localStorage: ${error}`);
}
const initialState: AuthSliceType = {
  saveCredentials: [],
  user: parsedUser,
  auth: undefined,
};

const authSlice = createSlice({
  name: "auth",
  initialState,
  reducers: {
    saveUser: (state, action) => {
      if (action.payload.talentData) {
        setCookie("TalentId", action.payload.talentData.id);
      }
      if (action.payload.companyData) {
        setCookie("CompanyId", action.payload.companyData.id);
      }
      setCookie("USER", action.payload.token);
      localStorage.setItem("USER", JSON.stringify(action.payload));
      state.user = action.payload;
    },
    logout: (state) => {
      state.user = null;
      deleteCookie("USER");
      deleteCookie("TalentId");
      deleteCookie("CompanyId");
      localStorage.removeItem("USER");
    },
  },
});

export const { saveUser, logout } = authSlice.actions;
export default authSlice.reducer;
