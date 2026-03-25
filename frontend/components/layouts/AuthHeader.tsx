import Link from "next/link";

import { Logo } from "../ui/Logo";
import { useRouter } from "next/navigation";

const AuthHeader = () => {
  const { push } = useRouter();
  return (
    <div className="py-[2rem] px-10  z-[1000]  bg-white">
      <div className="flex justify-between items-center  z-[1000] ">
        <Link href={"/"}>
          <Logo onClick={() => push("/")} />
        </Link>
      </div>
    </div>
  );
};
export default AuthHeader;
