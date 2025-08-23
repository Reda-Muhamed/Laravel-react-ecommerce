/* eslint-disable prettier/prettier */

import Carousel from "@/Components/Core/Carousel";
import CurrencyFormatter from "@/Components/Core/CurrencyFormatter";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Product, VariationTypeOption } from "@/types";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import React, { useEffect, useMemo, useState } from "react";

export default function Show({ product, variationsOptions }: { product: Product, variationsOptions: number[] }) {
  // console.log(product)
  // console.log("variationOptions",variationsOptions);
  /*
  1. useForm:
     - Manages the main form data that will be submitted to the server.
  */
  const form = useForm<{
    option_ids: Record<string, number>;
    quantity: number;
    price: number | null;

  }>({
    option_ids: {},
    quantity: 1,
    price: null,
  })
  const { url } = usePage();
  /*
     selectedOptions (useState):
     Keeps track of the user's currently selected variation options locally in the UI.
   */
  const [selectedOptions, setSelectedOptions] = useState<Record<number, VariationTypeOption>>({});
  useEffect(() => {
    if (!product?.variationTypes || product.variationTypes.length === 0) return;

    const newSelectedOptions: Record<number, VariationTypeOption> = {};

    for (const type of product.variationTypes) {
      if (!type?.options || type.options.length === 0) continue;

      const selectedOptionId = variationsOptions?.[type.id];
      const selectedOption = type.options.find(op => op.id === +selectedOptionId);

      // Only pick first option if nothing is provided
      if (selectedOption) {
        newSelectedOptions[type.id] = selectedOption;
      } else {
        newSelectedOptions[type.id] = type.options[0];
      }
    }

    setSelectedOptions(newSelectedOptions);
    // console.log('selectedOptions',selectedOptions)
  }, [product, variationsOptions]);

  const images = useMemo(() => {
    for (const typeId in selectedOptions) {
      const option = selectedOptions[typeId];
      if (option?.images?.length > 0) {
        return option.images;
      }
    }
    return product.images;
  }, [product, selectedOptions]);

  // console.log(selectedOptions)
  // console.log(images)
  const arraysAreEqual = (
    a: number[],
    b: number[]
  ) => {
    if (a.length !== b.length) return false;
    return a.every((val, index) => val === b[index]);
  }
  const computedProduct = useMemo(() => {
    const selectedOptionsIds = Object.values(selectedOptions)
      .map(option => option.id)
      .slice()
      .sort((a, b) => a - b); // ensure numeric sort
    // console.log("selected option ids: ",selectedOptions)
    // console.log("product variations: ",product.variations)

    for (const variation of product.variations) {
      const optionIds = variation.variation_type_option_ids.slice().sort((a, b) => a - b).map(e => +e);
      // console.log("option ids: ",optionIds)

      if (arraysAreEqual(selectedOptionsIds, optionIds)) {
        return {
          price: variation.price,
          quantity: variation.quantity === null ? Number.MAX_VALUE : variation.quantity,
        };
      }
    }

    return {
      price: product.price,
      quantity: product.quantity === null ? Number.MAX_VALUE : product.quantity,
    };
  }, [product, selectedOptions]);

  const chooseOption = (typeId: number, option: VariationTypeOption, updateRouter:
    boolean) => {
    setSelectedOptions((prevSelectedOptios) => {
      const newOptions = {
        ...prevSelectedOptios,
        [typeId]: option
      };
      if (updateRouter) {
        router.get(url, {
          options: getOptionIdMap(newOptions)
        }, {
          preserveScroll: true,
          preserveState: true,
        })
      }
      return newOptions;
    })
  }
  // useEffect(() => {
  //   if (userHasInteracted) return;
  //   if (!product?.variationTypes || product.variationTypes.length === 0) return;

  //   for (const type of product.variationTypes) {
  //     if (!type?.options || type.options.length === 0) continue;

  //     const selectedOptionId: number = variationOptions?.[type.id] ?? 0;
  //     const selectedOption = type.options.find(op => op.id === selectedOptionId) || type.options[0];

  //     chooseOption(type.id, selectedOption, false);
  //   }
  // }, [product, variationOptions, userHasInteracted]);

  useEffect(() => {
    const idsMap = Object.fromEntries(
      Object.entries(selectedOptions).map(([typeId, option]: [string, VariationTypeOption]) => [typeId, option.id])
    )
    // console.log(idsMap)
    form.setData('option_ids', idsMap)
  }, [selectedOptions])

  const getOptionIdMap = (newOptions: object) => {
    return Object.fromEntries(Object.entries(newOptions).map(([a, b]) => {
      return [a, b.id]
    }))
  }

  const onQuantityChange = (ev: React.ChangeEvent<HTMLSelectElement>) => {
    form.setData('quantity',
      parseInt(ev.target.value)
    )

  };

  const addToCart = () => {
    form.post(route('cart.store', product.id), {
      preserveScroll: true,
      preserveState: true,
      onError: (err) => {
        console.log(err);
      }
    })
    
  }
  const renderAddToCartButton = () => {
    return (
      <div className="mb-8 flex gap-4">
        <select value={form.data.quantity}
          onChange={onQuantityChange}
          className="select select-bordered w-full"
        >
          {Array.from({
            length: Math.min(10, computedProduct.quantity)
          }).map((el, i) => (
            <option key={i + 1} value={i + 1}>Quantity: {i + 1}</option>
          ))
          }
        </select>
        <button
          className="btn btn-primary"
          onClick={addToCart}
        >Add to Cart </button>

      </div>
    )

  }
  const renderProductVariationTypes = () => {
    return product.variationTypes.map((type) => (
      <div key={type.id}>
        <b className="px-2">{type.name}</b>
        {type.type === 'Image' && (
          <div className="flex gap-2 mb-4">
            {type.options.map((option) => (
              <div key={option.id} onClick={() => chooseOption(type.id, option, true)}>
                {option.images && (
                  <img
                    src={option.images[0].thumb}
                    alt={option.name}
                    className={`w-[50px]` + (selectedOptions[type.id]?.id === option.id ? 'outline outline-4 outline-primary' : '')}
                  />
                )}
              </div>
            ))}
          </div>
        )}
        {type.type === 'Radio' && (
          <div className="flex join mb-4">
            {type.options.map((option) => (
              <input
                key={option.id}
                onChange={() => chooseOption(type.id, option, true)}
                className="join-item btn"
                type="radio"
                value={option.id}
                checked={selectedOptions[type.id]?.id === option.id}
                name={'variation_type_' + type.id}
                aria-label={option.name}
              />
            ))}
          </div>
        )}
      </div>
    ));
  };


  return (
    <AuthenticatedLayout>
      <Head title={product.name} />
      <div className="container mx-auto p-8">
        <div className="grid gap-8 grid-cols-1 lg:grid-cols-12">
          <div className="col-span-7 ">
            <Carousel images={images} />
          </div>
          <div className="col-span-5">
            <h1 className="text-2xl mb-8">
              {product.name}
            </h1>
            <div>
              <div className="text-3xl font-semibold">
                <CurrencyFormatter amount={computedProduct.price} />
              </div>
            </div>
            {
              renderProductVariationTypes()
            }


            {computedProduct.quantity > 0 && computedProduct.quantity < 10 && (
              <div className="text-error my-4">
                <span className="text-red-500">Only {computedProduct.quantity} left in stock</span>
              </div>
            )}

            {
              renderAddToCartButton()
            }

            <b className="text-xl">About the Item</b>
            <div className="wysiwyg-output" dangerouslySetInnerHTML={{ __html: product.description }}></div>
          </div>
        </div>
      </div>

    </AuthenticatedLayout>


  )
}

