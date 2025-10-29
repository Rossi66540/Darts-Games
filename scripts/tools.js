function getPossibleFinish(score, lastMustBeDouble = true, remainingDarts = 3) {
    const singles = Array.from({ length: 20 }, (_, i) => ({ value: i + 1, label: `${i + 1}` }));
    const doubles = Array.from({ length: 20 }, (_, i) => ({ value: (i + 1) * 2, label: `D${i + 1}` }));
    const trebles = Array.from({ length: 20 }, (_, i) => ({ value: (i + 1) * 3, label: `T${i + 1}` }));
    const bull = [{ value: 25, label: 'S25' }, { value: 50, label: 'D25' }];

    const darts = [...singles, ...doubles, ...trebles, ...bull];
    const allowedLastDarts = lastMustBeDouble
        ? [...doubles, bull.find(d => d.label === 'D25')]
        : darts;

    // Vérifie en 1 fléchette
    if (remainingDarts >= 1) {
        for (const d1 of allowedLastDarts) {
            if (d1.value === score) return [d1.label];
        }
    }

    // Vérifie en 2 fléchettes
    if (remainingDarts >= 2) {
        for (const d1 of darts) {
            for (const d2 of allowedLastDarts) {
                if (d1.value + d2.value === score) return [d1.label, d2.label];
            }
        }
    }

    // Vérifie en 3 fléchettes
    if (remainingDarts >= 3) {
        for (const d1 of darts) {
            for (const d2 of darts) {
                for (const d3 of allowedLastDarts) {
                    if (d1.value + d2.value + d3.value === score) {
                        return [d1.label, d2.label, d3.label];
                    }
                }
            }
        }
    }

    return null; // Aucun finish possible
}
