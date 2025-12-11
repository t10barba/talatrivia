import Image from 'next/image';

export default function Header() {
  return (
    <header className="bg-[#27273f] w-full">
      <div className="container mx-auto px-4 py-4 flex items-center justify-between">
        <div className="flex items-center">
          <Image
            src="/images/logo-talana.png"
            alt="Talana Logo"
            width={150}
            height={40}
            priority
            className="h-10 w-auto"
          />
        </div>
        <nav>
          {/* Navigation items can be added here in the future */}
        </nav>
      </div>
    </header>
  );
}
