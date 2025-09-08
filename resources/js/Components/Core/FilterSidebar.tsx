/* eslint-disable prettier/prettier */
import { Category, Department } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import Checkbox from './Checkbox';
import PrimaryButton from './PrimaryButton';
import SecondaryButton from './SecondaryButton';


interface FilterProps {
  keyword: string | null;
  department_names: string[];
  category_names: string[];
  price_min: string | null;
  price_max: string | null;
}
interface FilterSidebarProps {
  departments?: Department[];
  categories?: Category[];
  showDepartments?: boolean;
  showCategories?: boolean;
  filters: FilterProps;
}

export default function FilterSidebar({
  departments = [],
  categories = [],
  showDepartments = true,
  showCategories = true,
  filters,
}: FilterSidebarProps) {
  // Initialize state from URL query parameters
  // console.log("=================================");
  // console.log('filters', filters);
  // console.log('departments', departments);
  // console.log('categories', categories);
  // console.log("=================================");


  const [selectedDepartments, setSelectedDepartments] = useState<string[]>(
    Array.isArray(filters.department_names) ? filters.department_names : []
  );

  const [selectedCategories, setSelectedCategories] = useState<string[]>(
    Array.isArray(filters.category_names) ? filters.category_names : []
  );

  const [priceMin, setPriceMin] = useState<string>(filters.price_min || '');
  const [priceMax, setPriceMax] = useState<string>(filters.price_max || '');
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const { url } = usePage();
  const currentDepartment = url?.split('/')[2]?.split('?')[0];
  // Update URL with all filters
  const updateUrl = () => {
    setIsLoading(true);

    // Get current search params from URL
    const params = new URLSearchParams(window.location.search);

    // Reset product filters but keep page if it exists
    params.delete("products_index[query]");
    params.delete("products_index[refinementList][department_name][0]");
    params.delete("products_index[refinementList][category_name][0]");
    params.delete("products_index[numericMenu][price][min]");
    params.delete("products_index[numericMenu][price][max]");

    if (filters.keyword) {
      params.set("products_index[query]", filters.keyword);
    }

    selectedDepartments.forEach((name, index) => {
      params.append(
        `products_index[refinementList][department_name][${index}]`,
        name
      );
    });

    selectedCategories.forEach((name, index) => {
      params.append(
        `products_index[refinementList][category_name][${index}]`,
        name
      );
    });

    if (priceMin && !isNaN(Number(priceMin))) {
      params.set("products_index[numericMenu][price][min]", priceMin);
    }
    if (priceMax && !isNaN(Number(priceMax))) {
      params.set("products_index[numericMenu][price][max]", priceMax);
    }

    // ⚡ Do NOT reset "page" here — let it stay in params if user clicked pagination
    router.get(`${window.location.pathname}?${params.toString()}`, {}, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onFinish: () => setIsLoading(false),
    });
  };


  // Update URL when filters change
  useEffect(() => {
    updateUrl();
  }, [selectedDepartments, selectedCategories]);

  // Toggle selection
  const toggleSelection = (name: string, type: 'department' | 'category') => {
    if (type === 'department') {
      setSelectedDepartments((prev) =>
        prev?.includes(name) ? prev.filter((d) => d !== name) : [...prev, name]
      );
    } else {
      setSelectedCategories((prev) =>
        prev?.includes(name) ? prev.filter((c) => c !== name) : [...prev, name]
      );
    }
  };

  // Price input validation
  const handlePriceChange = (value: string, field: 'priceMin' | 'priceMax') => {
    if (value === '' || (!isNaN(Number(value)) && Number(value) >= 0)) {
      if (field === 'priceMin') setPriceMin(value);
      else setPriceMax(value);
    }
  };

  // Price submission
  const handlePriceSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (priceMin && priceMax && Number(priceMin) > Number(priceMax)) {
      alert('Minimum price cannot exceed maximum price.');
      return;
    }
    updateUrl();
  };

  // Reset filters
  const resetFilters = () => {
    setSelectedDepartments([]);
    setSelectedCategories([]);
    setPriceMin('');
    setPriceMax('');
    router.get(window.location.pathname, {}, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onFinish: () => setIsLoading(false),
    });
  };

  return (
    <div className="flex flex-col gap-3 text-[#c1c1c1]">
      <div className="bg-[#15365711] text-[#c1c1c1] p-4 rounded-xl space-y-6 max-w-[300px] flex flex-col flex-wrap gap-2">
        <div>
          <div className='flex justify-between items-center mb-2'>
            <h3 className="font-bold mb-3">Current Filter</h3>
            {(selectedDepartments.length > 0 || selectedCategories.length > 0 || priceMin || priceMax) && (
              <SecondaryButton
                onClick={resetFilters}
                disabled={isLoading}
                className={` px-3 py-1 rounded-lg text-[#c1c1c1] hover:bg-red-700 ${isLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
              >
                Reset Filters
              </SecondaryButton>
            )}
          </div>
          {selectedDepartments.length === 0 && selectedCategories.length === 0 && !priceMin && !priceMax && (
            <p className="text-[#c1c1c1]">No filters applied.</p>
          )}
          <div className="flex flex-wrap flex-col gap-2">
            {selectedDepartments.length > 0 && <div className='inline-flex gap-2 flex-wrap overflow-wrap'>
              <h3 >Departments: </h3>
              <div className='flex  gap-2 flex-wrap'>
                {selectedDepartments.map((dep) => (
                  <div key={dep} className="bg-blue-600 text-[#c1c1c1] px-2 py-1 rounded-full text-sm ">
                    {dep}
                    {currentDepartment?.toLowerCase() != dep?.toLowerCase() && <span
                      className="ml-1 cursor-pointer"
                      onClick={() => toggleSelection(dep, 'department')}
                    > &times; </span>}
                  </div>
                ))}
              </div>
            </div>}

            {selectedCategories.length > 0 && <div className='inline-flex gap-2 flex-wrap overflow-wrap'>
              <h3 >Categories: </h3>
              <div className='flex  gap-2 flex-wrap'>
                {selectedCategories.map((cat) => (
                  <div key={cat} className="bg-blue-600 text-[#c1c1c1] px-2 py-1 rounded-full text-sm ">
                    {cat}
                    <span
                      className="ml-1 cursor-pointer"
                      onClick={() => toggleSelection(cat, 'category')}
                    > &times; </span>
                  </div>
                ))}
              </div>
            </div>}
            {(priceMin || priceMax) && (
              <div className="bg-blue-600 text-[#c1c1c1] px-3 py-1 rounded-full text-sm flex items-center gap-2">
                <span className="font-semibold">Price:</span>
                <span>
                  {priceMin && <>&ge; {priceMin}</>}
                  {priceMin && priceMax && ' - '}
                  {priceMax && <>&le; {priceMax}</>}
                </span>
              </div>
            )}

          </div>
        </div>
      </div>

      {showDepartments && (
        <div className="bg-[#15365711] text-[#c1c1c1] p-4 rounded-xl space-y-6 max-w-[300px] flex lex-col flex-wrap gap-2">
          <div>
            <h3 className="font-bold mb-3">Department</h3>
            {departments.map((dep) => (
              <label key={dep.id} className="block mb-2">
                <Checkbox
                  checked={selectedDepartments?.includes(dep.name)}
                  onChange={() => toggleSelection(dep.name, 'department')}
                  className="mr-2 w-4 h-4 rounded-md"
                  aria-label={`Filter by ${dep.name}`}
                  disabled={isLoading}
                />
                <span>
                  {dep.name} ({15})
                </span>
              </label>
            ))}
          </div>
        </div>
      )}


      {showCategories && (
        <div className="bg-[#15365711] text-[#c1c1c1] p-4 rounded-xl space-y-6 max-w-[300px] flex lex-col flex-wrap gap-2">
          <div>
            <h3 className="font-bold mb-3 text-[#c1c1c1]">Category</h3>
            {categories.map((cat) => (
              <label key={cat.id} className="block mb-2">
                <Checkbox
                  checked={selectedCategories.includes(cat.name)}
                  onChange={() => toggleSelection(cat.name, 'category')}
                  className="mr-2 w-4 h-4 rounded-md"
                  aria-label={`Filter by ${cat.name}`}
                  disabled={isLoading}
                />
                <span>
                  {cat.name} ({15})
                </span>
              </label>
            ))}
          </div>
        </div>
      )}


      <div className="bg-[#15365711] text-[#c1c1c1] p-4 rounded-xl space-y-6 max-w-[300px] flex lex-col flex-wrap">
        <h3 className="font-bold mb-0">Price Range</h3>
        <form onSubmit={handlePriceSubmit} className="flex gap-2 items-center">
          <input
            type="number"
            placeholder="Min"
            value={priceMin}
            onChange={(e) => handlePriceChange(e.target.value, 'priceMin')}
            className="w-20 text-black p-1 rounded"
            min="0"
            aria-label="Minimum price"
            disabled={isLoading}
          />
          <span>-</span>
          <input
            type="number"
            placeholder="Max"
            value={priceMax}
            onChange={(e) => handlePriceChange(e.target.value, 'priceMax')}
            className="w-20 text-black p-1 rounded"
            min="0"
            aria-label="Maximum price"
            disabled={isLoading}
          />
          <PrimaryButton
            type="submit"
            disabled={isLoading}
            className={`bg-indigo-500 px-3 py-1 rounded text-[#c1c1c1] hover:bg-indigo-600 ${isLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
          >
            Go
          </PrimaryButton>
        </form>
        {priceMin && priceMax && Number(priceMin) > Number(priceMax) && (
          <p className="text-red-400 text-sm mt-1">
            Minimum price cannot exceed maximum price.
          </p>
        )}
      </div>
    </div>
  );
}
