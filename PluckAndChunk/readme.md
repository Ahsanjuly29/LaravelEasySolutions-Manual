public function generateApiRequest($requestData)
    {
        // $hotelCodes = 
        $hotelCodesArray = HotelDetails::where('city_id', $requestData['cityId'])->pluck('hotel_code')->chunk(10);
        if (empty($hotelCodesArray)) 
        {
            Log::info('No hotel codes were found For this City Id');
            new Exception('No hotel codes were found For this City Id'); // if no Hotel Codes were found
        }

        $supplierRequests = [];
        foreach ($hotelCodesArray as $hotelCodes)
        {
            $requestData['Hotelcodes'] = implode("," , $hotelCodes->toArray());
            $supplierRequests[] = [
                'url'   => config('app.API_ENDPOINT').'/Search', // Api Path;
                'body'  => json_encode($requestData),
            ];
        }
       
        if (empty($supplierRequests))
        {
            Log::info('No Supplier Request were found For this City Id');
            new Exception('No Supplier Request were found For this City Id');
        }

        return $supplierRequests;
    }