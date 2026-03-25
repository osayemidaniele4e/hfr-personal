import CompleteProfileProgress from "@/components/ui/CompleteProfileProgress"

export function progressChange(percentageComplete: any) {
    if (percentageComplete === 50) {
      return (
        <CompleteProfileProgress
        bg="#FCA5A5"
          color="#EF4444"
          textColor="#EF4444"
          value={percentageComplete}
          progresslabel={percentageComplete}
        />
      );
    } else if (percentageComplete === 75) {
      return (
        <CompleteProfileProgress
        bg="#BFD3FE"
        textColor="#2563EB"
          color="#2563EB"
          value={percentageComplete}
          progresslabel={percentageComplete}
        />
      );
    } else {
     
      return (
        <CompleteProfileProgress
         bg="#86EFAC"
        textColor="#22C55E"
          color="#22C55E"
          value={percentageComplete ?? 0}
          progresslabel={percentageComplete ?? 0}
        />
        
      );
      // toast.success("Congratulations! Your profile is 100% complete. Well done!")
    }
  }