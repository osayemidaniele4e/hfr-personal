export const FlexRowContainer = ({ children }: { children: React.ReactNode }) => {
  return (
    <div className="flex mx-auto container flex-col lg:flex-row justify-center items-center gap-[2rem] lg:gap-[4rem] ">
      {children}
    </div>
  );
};
export const FlexRowReverseContainer = ({
  children,
}: {
  children: React.ReactNode;
}) => {
  return (
    <div className=" mx-auto container flex flex-col-reverse lg:flex-row justify-center items-center gap-5 lg:gap-[4rem] ">
      {children}
    </div>
  );
};


