import { CircularProgress, CircularProgressLabel,chakra } from "@chakra-ui/react";

const CompleteProfileProgress = ({ bg,textColor,value,progresslabel,color}:{value:number;textColor:string;progresslabel:number,color:string,bg:string}) => {
  return (
    <div className="flex  gap-2 justify-center items-center">
    <CircularProgress
    thickness={"7px"}
    size={"60px"}
      value={value}
      color={color}
      bg={bg}
      borderRadius={"full"}
    >
      <CircularProgressLabel fontWeight={"bold"} >
        {progresslabel}%
      </CircularProgressLabel>
    </CircularProgress>
    <chakra.p color={textColor}>Complete Profile</chakra.p>
  </div>
  )
}
export default CompleteProfileProgress
