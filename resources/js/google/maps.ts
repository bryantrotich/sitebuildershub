let loader: Promise<any> | null = null;

/**
 * Loads the Google Maps JavaScript API once and resolves with the global `google` object.
 * Libraries are then loaded on demand with `google.maps.importLibrary(...)`.
 */
export const loadGoogleMaps = (apiKey: string): Promise<any> => {
    if ((window as any).google?.maps?.importLibrary) {
        return Promise.resolve((window as any).google);
    }

    if (loader) {
        return loader;
    }

    loader = new Promise((resolve, reject) => {
        const callback = '__onGoogleMapsLoaded';

        (window as any)[callback] = () => resolve((window as any).google);

        const script   = document.createElement('script');
        script.src     = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&v=weekly&loading=async&callback=${callback}`;
        script.async   = true;
        script.onerror = () => {
            loader = null;
            reject(new Error('Unable to load Google Maps.'));
        };

        document.head.appendChild(script);
    });

    return loader;
}

export interface AddressParts {
    location: string,
    town:     string,
    county:   string,
    country:  string,
    lat?:     number,
    lng?:     number,
}

/**
 * Narrows Google address components down to town/city, county and country.
 *
 * Accepts both the Places API (New) shape ({ longText, types }) and the
 * Geocoder shape ({ long_name, types }).
 */
export const parseAddressComponents = (components: any[]): Omit<AddressParts, 'location'> => {
    const find = (...types: string[]) => {
        for (const type of types) {
            const component = components.find((item: any) => item.types.includes(type));

            if (component) {
                return component.longText ?? component.long_name ?? '';
            }
        }

        return '';
    };

    return {
        town:    find('locality', 'postal_town', 'administrative_area_level_3', 'sublocality_level_1', 'sublocality', 'administrative_area_level_2'),
        county:  find('administrative_area_level_1', 'administrative_area_level_2'),
        country: find('country'),
    };
}
