import { companyTalentRequest } from "@/types/admintype";
import { createSlice, PayloadAction } from "@reduxjs/toolkit";





const initialState: companyTalentRequest = {
  id: "",
  companyId: "",
  companyName: "",
  role: "",
  skills: [],
  jobType: "",
  talentLocation: "",
  numberOfResources: 0,
  levelOfExpertise: 0,
  yearsOfExperience: 0,
  additionalTalentService: "",
  status: 0,
};


const adminSlice = createSlice({
  name: "admin",
  initialState,
  reducers: {
    saveCompanyTalentData: (
      state,
      action: PayloadAction<companyTalentRequest>
    ) => {
      console.log(action.payload);
      return { ...action.payload };
    },
  },
});

export const { saveCompanyTalentData } = adminSlice.actions;
export default adminSlice.reducer;
