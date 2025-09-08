/* eslint-disable prettier/prettier */
import { PaginationProps } from "@/types";
import { Link } from "@inertiajs/react";

export default function Pagination<T>({ meta }: PaginationProps<T>) {
  if (!meta.links || meta.links.length === 0) return null;

  return (
    <div className="flex justify-center mt-8 mb-4">
      <div className="join">
        {meta.links.map((link, index) => {
          const isDisabled = !link.url;
          const isActive = link.active;

          return (
            <Link
              key={index}
              href={link.url || "#"}
              preserveScroll
              aria-label={link.label}
              className={`hidden sm:inline-flex join-item btn ${
                isActive ? "btn-primary text-white" : "btn-outline"
              } ${isDisabled ? "btn-disabled" : ""}`}
              dangerouslySetInnerHTML={{ __html: link.label }}
            />
          );
        })}

        {/* Mobile view: only show Previous, Current, Next */}
        <div className="sm:hidden flex join">
          {/* Previous */}
          <Link
            href={meta.links[0].url || "#"}
            preserveScroll
            className={`join-item btn ${
              !meta.links[0].url ? "btn-disabled" : "btn-outline"
            }`}
            dangerouslySetInnerHTML={{ __html: meta.links[0].label }}
          />

          {/* Current page */}
          <span className="join-item btn btn-primary text-white">
            {meta.current_page}
          </span>

          {/* Next */}
          <Link
            href={meta.links[meta.links.length - 1].url || "#"}
            preserveScroll
            className={`join-item btn ${
              !meta.links[meta.links.length - 1].url
                ? "btn-disabled"
                : "btn-outline"
            }`}
            dangerouslySetInnerHTML={{
              __html: meta.links[meta.links.length - 1].label,
            }}
          />
        </div>
      </div>
    </div>
  );
}
