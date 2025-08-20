/* eslint-disable prettier/prettier */
import { CartItem } from "./types";

// if the user choose a certian variation in the cart and need to see the same variation in the product detail page when hit the product in the cart 
export const productRoute=(item:CartItem)=>{
  const params = new URLSearchParams();
  Object.entries(item.option_ids).forEach(([typeId , optionId])=>{
    params.append(`options[${typeId}]`,optionId+'')

  })
  return route('product.show',item.slug)+'?'+params.toString();

}
